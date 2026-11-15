<?php
declare(strict_types=1);

namespace App\Central\Config;

use App\Central\CentralException;
use App\Central\Http\Origin;

/**
 * Central settings, read from the environment. No value here is a default for an operator-owned setting:
 * a missing or empty variable throws a CentralException that names the variable and never shows its value.
 */
final class CentralConfig
{
    /** Product id decided by the owner. The service audience is built from it. */
    public const SCHEDULED_PRODUCT_ID = 'scheduled';
    public const MIN_SECRET_LENGTH = 32;

    /** @var null|\Closure(string):?string */
    private readonly ?\Closure $lookup;

    /** @param null|\Closure(string):?string $lookup Test seam. Defaults to the process environment. */
    public function __construct(?\Closure $lookup = null)
    {
        $this->lookup = $lookup;
    }

    public function centralOrigin(): string
    {
        return Origin::of($this->required('CENTRAL_ORIGIN'), $this->allowInsecureLocalhost());
    }

    public function oidcDiscoveryUrl(): string
    {
        $url = $this->required('CENTRAL_OIDC_DISCOVERY_URL');
        Origin::assertSame($url, $this->centralOrigin(), $this->allowInsecureLocalhost());

        return $url;
    }

    public function oidcClientId(): string
    {
        return $this->required('CENTRAL_OIDC_CLIENT_ID');
    }

    public function oidcClientSecret(): string
    {
        return $this->required('CENTRAL_OIDC_CLIENT_SECRET');
    }

    public function oidcAudience(): string
    {
        return $this->required('CENTRAL_OIDC_AUDIENCE');
    }

    public function oidcRedirectUri(): string
    {
        $uri = $this->required('CENTRAL_OIDC_REDIRECT_URI');
        Origin::of($uri, $this->allowInsecureLocalhost());

        return $uri;
    }

    public function serviceClientId(): string
    {
        return $this->required('CENTRAL_SERVICE_CLIENT_ID');
    }

    public function serviceClientSecret(): string
    {
        return $this->required('CENTRAL_SERVICE_CLIENT_SECRET');
    }

    /** Client-credentials audience for Scheduled: urn:titan-central:product:<product id>. */
    public function serviceAudience(): string
    {
        return 'urn:titan-central:product:'.self::SCHEDULED_PRODUCT_ID;
    }

    public function eventsSecret(): string
    {
        $secret = $this->required('CENTRAL_EVENTS_HMAC_SECRET');
        if (strlen($secret) < self::MIN_SECRET_LENGTH) {
            throw new CentralException('config_invalid', 'CENTRAL_EVENTS_HMAC_SECRET must be at least '.self::MIN_SECRET_LENGTH.' characters.');
        }

        return $secret;
    }

    /**
     * Central sign-in is off unless CENTRAL_SIGNIN_ENABLED is exactly 1 or true (case-insensitive).
     * Any other value, including empty or unset, keeps the Central routes inert.
     */
    public function signInEnabled(): bool
    {
        $value = $this->lookup('CENTRAL_SIGNIN_ENABLED');

        return $value !== null && in_array(strtolower(trim($value)), ['1', 'true'], true);
    }

    /** The one organisation whose signed events this Scheduled installation accepts. */
    public function eventsOrganisationId(): string
    {
        $id = strtolower($this->required('CENTRAL_EVENTS_ORGANISATION_ID'));
        if (!preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/', $id)) {
            throw new CentralException('config_invalid', 'CENTRAL_EVENTS_ORGANISATION_ID must be a UUID.');
        }

        return $id;
    }

    /** The inbound event route is off unless CENTRAL_EVENTS_ENABLED is exactly 1 or true. */
    public function eventsEnabled(): bool
    {
        return in_array(strtolower((string) $this->lookup('CENTRAL_EVENTS_ENABLED')), ['1', 'true'], true);
    }

    public function allowInsecureLocalhost(): bool
    {
        return $this->lookup('TITAN_ALLOW_INSECURE_LOCALHOST') === '1';
    }

    private function required(string $name): string
    {
        $value = $this->lookup($name);
        if ($value === null || trim($value) === '') {
            throw new CentralException('config_missing', "Missing required environment variable {$name}.");
        }

        return trim($value);
    }

    private function lookup(string $name): ?string
    {
        if ($this->lookup !== null) {
            return ($this->lookup)($name);
        }
        $value = getenv($name);

        return $value === false ? null : $value;
    }
}
