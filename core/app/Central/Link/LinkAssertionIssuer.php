<?php
declare(strict_types=1);

namespace App\Central\Link;

use App\Central\CentralException;

/**
 * Signs a Scheduled link assertion for Central (compact JWS, EdDSA, typ titan-link+jwt).
 *
 * The private seed comes from the secret store through the caller. This class never reads the environment
 * and never logs the seed or the token. It is not called from any route yet, so it changes no behaviour.
 */
final class LinkAssertionIssuer
{
    public const TYP = 'titan-link+jwt';
    public const AUDIENCE = 'titan-central';
    public const MAX_TTL_SECONDS = 300;

    /** Claims Central must never receive from Scheduled in a link assertion. */
    public const FORBIDDEN_CLAIMS = [
        'email', 'email_verified', 'name', 'given_name', 'family_name', 'display_name', 'displayName',
        'preferred_username', 'username', 'nickname', 'phone_number',
    ];

    /** Claims the caller must supply. iss, aud, iat and exp are set here. */
    private const REQUIRED_CLAIMS = ['sub', 'org', 'lu', 'la', 'nonce'];

    private readonly string $seed;

    /** @param string $seedB64url 32-byte Ed25519 seed, base64url encoded. */
    public function __construct(
        private readonly string $productId,
        private readonly string $kid,
        string $seedB64url,
        private readonly NonceStore $nonces,
    ) {
        if (!preg_match('/\A[a-z][a-z0-9_-]*\z/', $productId)) {
            throw new CentralException('config_invalid', 'Product id is not valid.');
        }
        if ($kid === '') {
            throw new CentralException('config_invalid', 'Key id is empty.');
        }
        $seed = self::b64urlDecode($seedB64url);
        if ($seed === null || strlen($seed) !== SODIUM_CRYPTO_SIGN_SEEDBYTES) {
            throw new CentralException('config_invalid', 'Link signing seed must be 32 bytes.');
        }
        $this->seed = $seed;
    }

    /**
     * @param array<string,mixed> $claims
     * @return string Compact JWS.
     */
    public function issue(array $claims, int $now, int $ttlSeconds = self::MAX_TTL_SECONDS): string
    {
        if ($ttlSeconds < 1 || $ttlSeconds > self::MAX_TTL_SECONDS) {
            throw new CentralException('ttl_invalid', 'Link assertion lifetime must be 1 to 300 seconds.');
        }
        foreach (array_keys($claims) as $name) {
            if (in_array($name, self::FORBIDDEN_CLAIMS, true)) {
                throw new CentralException('forbidden_claim', "Claim \"{$name}\" must not be sent to Central.");
            }
        }
        foreach (self::REQUIRED_CLAIMS as $name) {
            if (!isset($claims[$name]) || !is_string($claims[$name]) || $claims[$name] === '') {
                throw new CentralException('claim_missing', "Claim \"{$name}\" is required.");
            }
        }
        if (!$this->nonces->consume($claims['nonce'], $now + $ttlSeconds)) {
            throw new CentralException('nonce_reused', 'Link assertion nonce was already used.');
        }

        $header = ['alg' => 'EdDSA', 'typ' => self::TYP, 'kid' => $this->kid];
        $payload = array_merge($claims, [
            'iss' => $this->productId,
            'aud' => self::AUDIENCE,
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
        ]);
        $signingInput = self::b64urlEncode((string) json_encode($header, JSON_UNESCAPED_SLASHES))
            .'.'.self::b64urlEncode((string) json_encode($payload, JSON_UNESCAPED_SLASHES));
        $keyPair = sodium_crypto_sign_seed_keypair($this->seed);
        $signature = sodium_crypto_sign_detached($signingInput, sodium_crypto_sign_secretkey($keyPair));

        return $signingInput.'.'.self::b64urlEncode($signature);
    }

    /** Public JWK for the Central operator. Public key only. */
    public function publicJwk(): array
    {
        $publicKey = sodium_crypto_sign_publickey(sodium_crypto_sign_seed_keypair($this->seed));

        return ['kty' => 'OKP', 'crv' => 'Ed25519', 'x' => self::b64urlEncode($publicKey), 'kid' => $this->kid, 'alg' => 'EdDSA', 'use' => 'sig'];
    }

    public static function b64urlEncode(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    public static function b64urlDecode(string $encoded): ?string
    {
        if (!preg_match('/\A[A-Za-z0-9_-]*\z/', $encoded)) {
            return null;
        }
        $decoded = base64_decode(strtr($encoded, '-_', '+/').str_repeat('=', (4 - strlen($encoded) % 4) % 4), true);

        return $decoded === false ? null : $decoded;
    }
}
