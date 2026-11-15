<?php
declare(strict_types=1);

namespace App\Central\Oidc;

use App\Central\CentralException;

/**
 * Verifies the signature of a compact ID token against Central's JWKS (Ed25519 keys, alg EdDSA only).
 * Any other algorithm, a missing or unknown key, or a bad signature fails closed. The caller still runs
 * IdTokenClaims::assertValid on the returned payload.
 */
final class IdTokenVerifier
{
    private const MAX_TOKEN_BYTES = 8192;

    /**
     * @param array<mixed> $jwks The parsed JWKS document ({"keys": [...]}).
     * @return array<string,mixed> The verified payload.
     */
    public static function verify(string $jwt, array $jwks): array
    {
        if (strlen($jwt) > self::MAX_TOKEN_BYTES) {
            throw new CentralException('token_malformed', 'ID token is too large.');
        }
        $parts = explode('.', $jwt);
        if (count($parts) !== 3 || $parts[0] === '' || $parts[1] === '' || $parts[2] === '') {
            throw new CentralException('token_malformed', 'ID token is not a compact JWS.');
        }
        [$h64, $p64, $s64] = $parts;
        $header = self::decodeJson($h64);
        $payload = self::decodeJson($p64);
        $signature = self::b64urlDecode($s64);
        if (($header['alg'] ?? null) !== 'EdDSA') {
            throw new CentralException('bad_algorithm', 'ID token algorithm is not EdDSA.');
        }
        if ($signature === null || strlen($signature) !== SODIUM_CRYPTO_SIGN_BYTES) {
            throw new CentralException('token_malformed', 'ID token signature has the wrong length.');
        }
        $key = self::keyFor($jwks, $header['kid'] ?? null);
        if (!sodium_crypto_sign_verify_detached($signature, $h64.'.'.$p64, $key)) {
            throw new CentralException('bad_signature', 'ID token signature did not verify.');
        }

        return $payload;
    }

    /** @param array<mixed> $jwks */
    private static function keyFor(array $jwks, mixed $kid): string
    {
        $keys = $jwks['keys'] ?? null;
        if (!is_array($keys)) {
            throw new CentralException('unknown_key', 'JWKS has no keys.');
        }
        $matches = [];
        foreach ($keys as $jwk) {
            if (!is_array($jwk) || ($jwk['kty'] ?? null) !== 'OKP' || ($jwk['crv'] ?? null) !== 'Ed25519' || !is_string($jwk['x'] ?? null)) {
                continue;
            }
            if ($kid !== null && ($jwk['kid'] ?? null) !== $kid) {
                continue;
            }
            if (isset($jwk['alg']) && $jwk['alg'] !== 'EdDSA') {
                continue;
            }
            $raw = self::b64urlDecode($jwk['x']);
            if ($raw !== null && strlen($raw) === SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
                $matches[] = $raw;
            }
        }
        if (count($matches) !== 1) {
            throw new CentralException('unknown_key', 'No single Central signing key matches this ID token.');
        }

        return $matches[0];
    }

    /** @return array<string,mixed> */
    private static function decodeJson(string $segment): array
    {
        $raw = self::b64urlDecode($segment);
        $data = $raw === null ? null : json_decode($raw, true);
        if (!is_array($data)) {
            throw new CentralException('token_malformed', 'ID token segment is not a JSON object.');
        }

        return $data;
    }

    private static function b64urlDecode(string $value): ?string
    {
        if (!preg_match('/\A[A-Za-z0-9\-_]*\z/', $value)) {
            return null;
        }
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return $decoded === false ? null : $decoded;
    }
}
