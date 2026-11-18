<?php
declare(strict_types=1);

namespace App\Central\Oidc;

use App\Central\CentralException;

/**
 * Claim checks for an ID token: issuer, audience, expiry, nonce and subject.
 *
 * Callers MUST verify the token signature first with IdTokenVerifier::verify against the jwks_uri from
 * discovery. CentralSignIn::complete does this before it calls assertValid. This class only checks claims
 * of an already-verified token.
 */
final class IdTokenClaims
{
    public static function assertValid(
        array $claims,
        string $issuer,
        string $audience,
        string $expectedNonce,
        int $now,
        int $leewaySeconds = 60,
    ): void {
        if (($claims['iss'] ?? null) !== $issuer) {
            throw new CentralException('wrong_issuer', 'ID token issuer does not match Central.');
        }
        $aud = $claims['aud'] ?? null;
        $audiences = is_array($aud) ? $aud : [$aud];
        if (!in_array($audience, $audiences, true)) {
            throw new CentralException('wrong_audience', 'ID token audience does not match Scheduled.');
        }
        if (!isset($claims['exp']) || !is_int($claims['exp'])) {
            throw new CentralException('missing_expiry', 'ID token has no expiry.');
        }
        if ($now > $claims['exp'] + $leewaySeconds) {
            throw new CentralException('expired', 'ID token has expired.');
        }
        if (isset($claims['iat']) && is_int($claims['iat']) && $claims['iat'] > $now + $leewaySeconds) {
            throw new CentralException('issued_in_future', 'ID token was issued in the future.');
        }
        if (!is_string($claims['nonce'] ?? null) || $expectedNonce === '' || !hash_equals($expectedNonce, $claims['nonce'])) {
            throw new CentralException('nonce_mismatch', 'ID token nonce does not match the sign-in request.');
        }
        if (!is_string($claims['sub'] ?? null) || $claims['sub'] === '') {
            throw new CentralException('missing_subject', 'ID token has no subject.');
        }
    }
}
