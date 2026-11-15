<?php
declare(strict_types=1);

namespace App\Central\Oidc;

use App\Central\CentralException;

/**
 * Builds the authorization-code + PKCE request. The caller stores state, nonce and code verifier server-side
 * (never in a URL or a browser-readable cookie) and compares the returned state with assertState().
 */
final class AuthorizationRequest
{
    public const SCOPE = 'openid profile email';

    private function __construct(
        public readonly string $url,
        public readonly string $state,
        public readonly string $nonce,
        public readonly string $codeVerifier,
    ) {
    }

    public static function create(OidcDiscovery $discovery, string $clientId, string $redirectUri): self
    {
        $verifier = Pkce::verifier();
        $state = Pkce::randomToken();
        $nonce = Pkce::randomToken();
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => self::SCOPE,
            'state' => $state,
            'nonce' => $nonce,
            'code_challenge' => Pkce::challenge($verifier),
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return new self($discovery->authorizationEndpoint().'?'.$query, $state, $nonce, $verifier);
    }

    public static function assertState(string $expected, ?string $returned): void
    {
        if ($expected === '' || $returned === null || !hash_equals($expected, $returned)) {
            throw new CentralException('state_mismatch', 'OIDC state did not match the sign-in request.');
        }
    }
}
