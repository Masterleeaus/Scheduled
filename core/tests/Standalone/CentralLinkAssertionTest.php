<?php
declare(strict_types=1);

// Standalone check for the Scheduled -> Central link assertion issuer. Fakes only: no network, no database.
// Run: php core/tests/Standalone/CentralLinkAssertionTest.php

require_once __DIR__.'/../../app/Central/CentralException.php';
require_once __DIR__.'/../../app/Central/Oidc/IdTokenVerifier.php';
require_once __DIR__.'/../../app/Central/Link/NonceStore.php';
require_once __DIR__.'/../../app/Central/Link/InMemoryNonceStore.php';
require_once __DIR__.'/../../app/Central/Link/LinkAssertionIssuer.php';

use App\Central\CentralException;
use App\Central\Link\InMemoryNonceStore;
use App\Central\Link\LinkAssertionIssuer;
use App\Central\Oidc\IdTokenVerifier;

function check(bool $ok, string $message): void
{
    if (!$ok) {
        throw new RuntimeException('FAIL: '.$message);
    }
}

function refused(callable $fn, string $reason, string $message): void
{
    try {
        $fn();
    } catch (CentralException $e) {
        check($e->reason === $reason, $message.' (got '.$e->reason.')');

        return;
    }
    throw new RuntimeException('FAIL: '.$message.' (no exception)');
}

$seed = LinkAssertionIssuer::b64urlEncode(str_repeat("\x07", 32));
$now = time();
$org = '6f1c2a3e-1111-4222-8333-444455556666';
$claims = ['sub' => 'central-sub-1', 'org' => $org, 'lu' => 'lu-1', 'la' => 'la-1', 'nonce' => 'n-1'];

$issuer = new LinkAssertionIssuer('scheduled', 'k1', $seed, new InMemoryNonceStore());
$jwt = $issuer->issue($claims, $now);

// Round trip: the token verifies against the public JWK only.
$jwks = ['keys' => [$issuer->publicJwk()]];
$payload = IdTokenVerifier::verify($jwt, $jwks);
check($payload['iss'] === 'scheduled' && $payload['aud'] === 'titan-central', 'iss/aud set');
check($payload['sub'] === 'central-sub-1' && $payload['org'] === $org, 'subject and org carried');
check($payload['exp'] - $payload['iat'] === 300, 'default lifetime is 300 s');

// Header: EdDSA, typ titan-link+jwt, kid.
[$h64] = explode('.', $jwt);
$header = json_decode(base64_decode(strtr($h64, '-_', '+/')), true);
check($header === ['alg' => 'EdDSA', 'typ' => 'titan-link+jwt', 'kid' => 'k1'], 'header fields');

// Tampered payload fails signature.
[$h, , $s] = explode('.', $jwt);
$forged = LinkAssertionIssuer::b64urlEncode(json_encode(['iss' => 'scheduled', 'aud' => 'titan-central', 'iat' => $now, 'exp' => $now + 60, 'sub' => 'other', 'org' => $org, 'lu' => 'x', 'la' => 'y', 'nonce' => 'z']));
refused(fn () => IdTokenVerifier::verify($h.'.'.$forged.'.'.$s, $jwks), 'bad_signature', 'tampered payload refused');

// Wrong public key refused.
$otherJwks = ['keys' => [array_merge($issuer->publicJwk(), ['x' => LinkAssertionIssuer::b64urlEncode(str_repeat("\x00", 32))])]];
refused(fn () => IdTokenVerifier::verify($jwt, $otherJwks), 'bad_signature', 'wrong public key refused');

// Forbidden claims refused, one per name.
foreach (LinkAssertionIssuer::FORBIDDEN_CLAIMS as $name) {
    $bad = array_merge($claims, [$name => 'x', 'nonce' => 'forbidden-'.$name]);
    refused(fn () => $issuer->issue($bad, $now), 'forbidden_claim', "forbidden claim {$name} refused");
}

// 300 s limit.
refused(fn () => $issuer->issue(array_merge($claims, ['nonce' => 'ttl-1']), $now, 301), 'ttl_invalid', 'lifetime above 300 s refused');
refused(fn () => $issuer->issue(array_merge($claims, ['nonce' => 'ttl-2']), $now, 0), 'ttl_invalid', 'zero lifetime refused');

// Required claims.
refused(fn () => $issuer->issue(array_diff_key($claims, ['org' => 1]), $now), 'claim_missing', 'missing org refused');

// Single-use nonce.
$issuer->issue(array_merge($claims, ['nonce' => 'once']), $now);
refused(fn () => $issuer->issue(array_merge($claims, ['nonce' => 'once']), $now), 'nonce_reused', 'reused nonce refused');

// Bad seed and bad product id refused at construction.
refused(fn () => new LinkAssertionIssuer('scheduled', 'k1', 'short', new InMemoryNonceStore()), 'config_invalid', 'short seed refused');
refused(fn () => new LinkAssertionIssuer('Scheduled!', 'k1', $seed, new InMemoryNonceStore()), 'config_invalid', 'bad product id refused');

// Public JWK carries no private material.
check(!array_key_exists('d', $issuer->publicJwk()), 'public JWK has no private part');

echo "CentralLinkAssertionTest: all checks passed\n";
