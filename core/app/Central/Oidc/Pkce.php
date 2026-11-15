<?php
declare(strict_types=1);

namespace App\Central\Oidc;

use App\Central\CentralException;

/** PKCE (RFC 7636, S256) and random state/nonce helpers. */
final class Pkce
{
    public static function verifier(): string
    {
        return self::base64Url(random_bytes(32));
    }

    public static function challenge(string $verifier): string
    {
        self::assertVerifier($verifier);

        return self::base64Url(hash('sha256', $verifier, true));
    }

    public static function randomToken(): string
    {
        return self::base64Url(random_bytes(32));
    }

    public static function assertVerifier(string $verifier): void
    {
        if (!preg_match('/\A[A-Za-z0-9\-._~]{43,128}\z/', $verifier)) {
            throw new CentralException('pkce_invalid', 'PKCE code verifier has an invalid length or character set.');
        }
    }

    private static function base64Url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
