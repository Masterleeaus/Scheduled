<?php
declare(strict_types=1);

namespace App\Central\Oidc;

use App\Central\CentralException;
use App\Central\Http\HttpTransport;
use App\Central\Http\Origin;

/**
 * Reads the OIDC discovery document once per process. Every endpoint must be on CENTRAL_ORIGIN, and the
 * issuer is taken from the document, so a token from another issuer is refused later.
 */
final class OidcDiscovery
{
    private const REQUIRED = ['issuer', 'authorization_endpoint', 'token_endpoint', 'jwks_uri'];

    /** @var null|array<string,mixed> */
    private ?array $document = null;

    public function __construct(
        private readonly HttpTransport $http,
        private readonly string $discoveryUrl,
        private readonly string $origin,
        private readonly bool $allowInsecureLocalhost = false,
    ) {
    }

    /** @return array<string,mixed> */
    public function document(): array
    {
        if ($this->document !== null) {
            return $this->document;
        }
        $response = $this->http->getJson($this->discoveryUrl);
        if ($response['status'] !== 200 || $response['json'] === null) {
            throw new CentralException('discovery_failed', 'OIDC discovery did not return a configuration document.');
        }
        $doc = $response['json'];
        foreach (self::REQUIRED as $field) {
            if (!isset($doc[$field]) || !is_string($doc[$field]) || $doc[$field] === '') {
                throw new CentralException('discovery_incomplete', "OIDC discovery document has no {$field}.");
            }
            if ($field !== 'issuer') {
                Origin::assertSame($doc[$field], $this->origin, $this->allowInsecureLocalhost);
            }
        }
        Origin::assertSame($doc['issuer'], $this->origin, $this->allowInsecureLocalhost);

        return $this->document = $doc;
    }

    public function issuer(): string
    {
        return (string) $this->document()['issuer'];
    }

    public function authorizationEndpoint(): string
    {
        return (string) $this->document()['authorization_endpoint'];
    }

    public function tokenEndpoint(): string
    {
        return (string) $this->document()['token_endpoint'];
    }

    public function jwksUri(): string
    {
        return (string) $this->document()['jwks_uri'];
    }
}
