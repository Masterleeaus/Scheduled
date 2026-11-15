<?php
declare(strict_types=1);

namespace App\Central\Service;

use App\Central\CentralException;
use App\Central\Http\HttpTransport;
use App\Central\Http\Origin;
use App\Central\Oidc\OidcDiscovery;

/**
 * Client-credentials token for Scheduled's own calls to Central. Tokens are cached in memory and refreshed
 * before expiry. Token contents, the client secret and response bodies are never logged or put in messages.
 *
 * Central verifies the token signature, audience and expiry on every call. This client also refuses a token
 * whose audience is not Scheduled's, so a wrong token is caught before it is used.
 */
final class ServiceTokenClient
{
    /** Keys Central refuses in a query string or body. Tenant and cohort come from the verified token only. */
    public const FORBIDDEN_PARAMS = [
        'organisation_id', 'organisationId', 'tenant', 'cohort', 'cohorts',
        'titan_organisation_id', 'titan_cohorts',
    ];

    private const REFRESH_MARGIN_SECONDS = 60;

    /** @var null|array{token:string, expiresAt:int} */
    private ?array $cached = null;

    /** @param null|\Closure():int $clock */
    public function __construct(
        private readonly HttpTransport $http,
        private readonly OidcDiscovery $discovery,
        private readonly string $origin,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $audience,
        private readonly bool $allowInsecureLocalhost = false,
        private readonly ?\Closure $clock = null,
    ) {
    }

    public function accessToken(): string
    {
        $now = $this->now();
        if ($this->cached !== null && $now < $this->cached['expiresAt'] - self::REFRESH_MARGIN_SECONDS) {
            return $this->cached['token'];
        }
        $response = $this->http->postForm(
            $this->discovery->tokenEndpoint(),
            ['grant_type' => 'client_credentials', 'audience' => $this->audience],
            ['Authorization: Basic '.base64_encode($this->clientId.':'.$this->clientSecret)],
        );
        if ($response['status'] !== 200 || !is_array($response['json'])) {
            throw new CentralException('token_failed', 'Central did not issue a service token.');
        }
        $token = $response['json']['access_token'] ?? null;
        $expiresIn = $response['json']['expires_in'] ?? null;
        if (!is_string($token) || $token === '' || !is_int($expiresIn) || $expiresIn < 1) {
            throw new CentralException('token_invalid', 'Central returned an unusable service token.');
        }
        self::assertAudience($token, $this->audience, $now);
        $this->cached = ['token' => $token, 'expiresAt' => $now + $expiresIn];

        return $token;
    }

    /**
     * Authenticated GET to a Central URL on CENTRAL_ORIGIN. The query must not carry tenant or cohort fields.
     *
     * @return array<mixed>
     */
    public function getJson(string $url): array
    {
        Origin::assertSame($url, $this->origin, $this->allowInsecureLocalhost);
        $query = parse_url($url, PHP_URL_QUERY);
        if (is_string($query)) {
            parse_str($query, $params);
            self::assertNoTenantParams($params);
        }
        $response = $this->http->getJson($url, ['Authorization: Bearer '.$this->accessToken()]);
        if ($response['status'] !== 200 || $response['json'] === null) {
            throw new CentralException('call_failed', 'Central call did not succeed.');
        }

        return $response['json'];
    }

    /**
     * Refuses tenant or cohort fields anywhere in a query or body, at any depth. Central answers 400 for them,
     * so failing here keeps a bad request from leaving the process.
     *
     * @param array<mixed> $params
     */
    public static function assertNoTenantParams(array $params): void
    {
        foreach ($params as $key => $value) {
            if (is_string($key) && in_array($key, self::FORBIDDEN_PARAMS, true)) {
                throw new CentralException('tenant_param_forbidden', "Tenant or cohort field \"{$key}\" must not be sent to Central.");
            }
            if (is_array($value)) {
                self::assertNoTenantParams($value);
            }
        }
    }

    /**
     * Refuses a token that is not a JWT for this audience, or is already expired. The payload is decoded but not
     * signature-checked here: Central verifies the signature. An opaque token is refused, because its audience
     * cannot be checked.
     */
    public static function assertAudience(string $jwt, string $audience, int $now): void
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new CentralException('token_not_jwt', 'Service token is not a JWT, so its audience cannot be checked.');
        }
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/'), true) ?: '', true);
        if (!is_array($payload)) {
            throw new CentralException('token_invalid', 'Service token payload is unreadable.');
        }
        $aud = $payload['aud'] ?? null;
        $audiences = is_array($aud) ? $aud : [$aud];
        if (!in_array($audience, $audiences, true)) {
            throw new CentralException('wrong_audience', 'Service token audience is not Scheduled.');
        }
        if (!isset($payload['exp']) || !is_int($payload['exp']) || $payload['exp'] <= $now) {
            throw new CentralException('token_expired', 'Service token is expired.');
        }
    }

    private function now(): int
    {
        return $this->clock !== null ? ($this->clock)() : time();
    }
}
