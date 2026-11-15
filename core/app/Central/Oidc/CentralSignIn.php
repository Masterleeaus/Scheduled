<?php
declare(strict_types=1);

namespace App\Central\Oidc;

use App\Central\CentralException;
use App\Central\Http\HttpTransport;

/**
 * Authorization-code + PKCE sign-in against Central. The code is exchanged over the back channel (server to
 * token endpoint, client authentication, PKCE verifier). The ID token is then checked for signature (EdDSA,
 * Central JWKS), issuer, audience, expiry, nonce and subject. Pending state is single-use: the caller must
 * discard it after one call to complete(), whether or not that call succeeds.
 */
final class CentralSignIn
{
    public const SESSION_KEY = 'central_signin';

    /** @param null|\Closure():int $clock */
    public function __construct(
        private readonly HttpTransport $http,
        private readonly OidcDiscovery $discovery,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $redirectUri,
        private readonly string $audience,
        private readonly ?\Closure $clock = null,
    ) {
    }

    public function begin(): AuthorizationRequest
    {
        return AuthorizationRequest::create($this->discovery, $this->clientId, $this->redirectUri);
    }

    /**
     * @param array<string,mixed> $pending state, nonce and code_verifier stored at begin()
     * @return array{iss:string, sub:string} verified issuer and subject
     */
    public function complete(array $pending, mixed $returnedState, mixed $code): array
    {
        $state = $pending['state'] ?? '';
        $nonce = $pending['nonce'] ?? '';
        $verifier = $pending['code_verifier'] ?? '';
        if (!is_string($state) || !is_string($nonce) || !is_string($verifier) || $nonce === '' || $verifier === '') {
            throw new CentralException('no_pending_signin', 'No sign-in is pending for this browser.');
        }
        AuthorizationRequest::assertState($state, is_string($returnedState) ? $returnedState : null);
        if (!is_string($code) || $code === '' || strlen($code) > 2048) {
            throw new CentralException('code_missing', 'Central did not return an authorization code.');
        }

        $token = $this->http->postForm(
            $this->discovery->tokenEndpoint(),
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'code_verifier' => $verifier,
            ],
            ['Authorization: Basic '.base64_encode($this->clientId.':'.$this->clientSecret)],
        );
        $idToken = $token['json']['id_token'] ?? null;
        if ($token['status'] !== 200 || !is_string($idToken) || $idToken === '') {
            throw new CentralException('token_exchange_failed', 'Central did not issue an ID token.');
        }

        $jwks = $this->http->getJson($this->discovery->jwksUri());
        if ($jwks['status'] !== 200 || !is_array($jwks['json'])) {
            throw new CentralException('jwks_failed', 'Central signing keys could not be read.');
        }
        $claims = IdTokenVerifier::verify($idToken, $jwks['json']);

        $aud = $claims['aud'] ?? null;
        if (is_array($aud) && count($aud) > 1 && ($claims['azp'] ?? null) !== $this->clientId) {
            throw new CentralException('wrong_audience', 'ID token has several audiences and no matching azp.');
        }
        $now = $this->clock !== null ? ($this->clock)() : time();
        IdTokenClaims::assertValid($claims, $this->discovery->issuer(), $this->audience, $nonce, $now);

        return ['iss' => $this->discovery->issuer(), 'sub' => (string) $claims['sub']];
    }
}
