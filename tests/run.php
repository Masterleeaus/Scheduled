<?php
declare(strict_types=1);

/* Unit tests for the Central integration. No network, no database. Run: composer test */

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = dirname(__DIR__).'/core/app/'.str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
    if (is_file($file)) {
        require $file;
    }
});

use App\Central\CentralException;
use App\Central\Config\CentralConfig;
use App\Central\Events\EventConsumer;
use App\Central\Events\InMemoryProcessedEventStore;
use App\Central\Events\SignedEventVerifier;
use App\Central\Flags\FlagReader;
use App\Central\Http\HttpTransport;
use App\Central\Link\NoExistingLinkResolver;
use App\Central\Oidc\CentralSignIn;
use App\Central\Oidc\IdTokenVerifier;
use App\Central\Oidc\AuthorizationRequest;
use App\Central\Oidc\IdTokenClaims;
use App\Central\Oidc\OidcDiscovery;
use App\Central\Oidc\Pkce;
use App\Central\Service\ServiceTokenClient;

final class FakeHttp implements HttpTransport
{
    /** @var list<array{string,string,array}> */
    public array $calls = [];

    /** @param list<array{status:int, json:?array}> $responses */
    public function __construct(private array $responses = [])
    {
    }

    public function postForm(string $url, array $fields, array $headers = []): array
    {
        $this->calls[] = ['POST', $url, $headers];

        return array_shift($this->responses) ?? ['status' => 599, 'json' => null];
    }

    public function getJson(string $url, array $headers = []): array
    {
        $this->calls[] = ['GET', $url, $headers];

        return array_shift($this->responses) ?? ['status' => 599, 'json' => null];
    }
}

$passed = 0;
$failures = [];
function check(bool $condition, string $label): void
{
    global $passed, $failures;
    if ($condition) {
        $passed++;
    } else {
        $failures[] = $label;
    }
}
function reasonOf(callable $fn): ?string
{
    try {
        $fn();
    } catch (CentralException $e) {
        return $e->reason;
    }

    return null;
}
function b64url(string $bytes): string
{
    return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
}
function jwt(array $payload): string
{
    return b64url('{"alg":"none"}').'.'.b64url(json_encode($payload)).'.'.b64url('sig');
}
function envFrom(array $env): CentralConfig
{
    return new CentralConfig(static fn (string $name): ?string => $env[$name] ?? null);
}
function discoveryDoc(string $origin = 'https://central.example'): array
{
    return [
        'issuer' => $origin.'/oidc',
        'authorization_endpoint' => $origin.'/oidc/auth',
        'token_endpoint' => $origin.'/oidc/token',
        'jwks_uri' => $origin.'/oidc/jwks',
    ];
}
function signedBody(array $unsigned, string $secret): string
{
    $object = json_decode(json_encode($unsigned), false, 32, JSON_THROW_ON_ERROR);
    $object->signature = hash_hmac('sha256', SignedEventVerifier::canonicalJson($object), $secret);

    return json_encode($object, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

// --- Config -----------------------------------------------------------------
$cfg = envFrom([]);
$e = null;
try {
    $cfg->centralOrigin();
} catch (CentralException $e) {
}
check($e?->reason === 'config_missing' && str_contains($e->getMessage(), 'CENTRAL_ORIGIN'), 'config: missing origin names the variable');
check(reasonOf(fn () => envFrom(['CENTRAL_ORIGIN' => 'http://central.example'])->centralOrigin()) === 'insecure_url', 'config: http origin refused');
check(envFrom(['CENTRAL_ORIGIN' => 'https://central.example/'])->centralOrigin() === 'https://central.example', 'config: origin normalised');
check(envFrom(['CENTRAL_ORIGIN' => 'http://localhost:8787', 'TITAN_ALLOW_INSECURE_LOCALHOST' => '1'])->centralOrigin() === 'http://localhost:8787', 'config: localhost http allowed only in dev');
check(reasonOf(fn () => envFrom(['CENTRAL_ORIGIN' => 'https://central.example', 'CENTRAL_OIDC_DISCOVERY_URL' => 'https://other.example/.well-known'])->oidcDiscoveryUrl()) === 'origin_mismatch', 'config: discovery must be on origin');
check(reasonOf(fn () => envFrom(['CENTRAL_EVENTS_HMAC_SECRET' => 'short'])->eventsSecret()) === 'config_invalid', 'config: short event secret refused');
check(envFrom(['CENTRAL_EVENTS_HMAC_SECRET' => str_repeat('k', 32)])->eventsSecret() === str_repeat('k', 32), 'config: 32-char secret accepted');
check(envFrom([])->serviceAudience() === 'urn:titan-central:product:scheduled', 'config: service audience from product id');
check(reasonOf(fn () => envFrom(['CENTRAL_OIDC_CLIENT_SECRET' => '   '])->oidcClientSecret()) === 'config_missing', 'config: blank value treated as unset');

// --- PKCE and authorization request ----------------------------------------
check(Pkce::challenge('dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk') === 'E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM', 'pkce: RFC 7636 S256 vector');
check(strlen(Pkce::verifier()) === 43, 'pkce: verifier length');
check(reasonOf(fn () => Pkce::assertVerifier('too-short')) === 'pkce_invalid', 'pkce: short verifier refused');

$http = new FakeHttp([['status' => 200, 'json' => discoveryDoc()]]);
$discovery = new OidcDiscovery($http, 'https://central.example/oidc/.well-known/openid-configuration', 'https://central.example');
$request = AuthorizationRequest::create($discovery, 'scheduled-client', 'https://scheduled.example/auth/callback');
parse_str((string) parse_url($request->url, PHP_URL_QUERY), $q);
check(str_starts_with($request->url, 'https://central.example/oidc/auth?'), 'oidc: authorize endpoint from discovery');
check(($q['code_challenge_method'] ?? '') === 'S256' && ($q['response_type'] ?? '') === 'code', 'oidc: PKCE S256 code flow');
check(($q['state'] ?? '') === $request->state && ($q['nonce'] ?? '') === $request->nonce, 'oidc: state and nonce sent');
check(!str_contains($request->url, $request->codeVerifier), 'oidc: code verifier not in browser URL');
check(reasonOf(fn () => AuthorizationRequest::assertState('abc', 'abd')) === 'state_mismatch', 'oidc: state mismatch refused');
check(reasonOf(fn () => AuthorizationRequest::assertState('abc', null)) === 'state_mismatch', 'oidc: missing state refused');
AuthorizationRequest::assertState('abc', 'abc');
check(true, 'oidc: matching state accepted');

check(reasonOf(fn () => (new OidcDiscovery(new FakeHttp([['status' => 200, 'json' => array_merge(discoveryDoc('https://evil.example'), ['token_endpoint' => 'https://evil.example/token'])]]), 'https://central.example/d', 'https://central.example'))->document()) === 'origin_mismatch', 'oidc: cross-origin endpoint refused');
check(reasonOf(fn () => (new OidcDiscovery(new FakeHttp([['status' => 200, 'json' => ['issuer' => 'https://central.example/oidc']]]), 'https://central.example/d', 'https://central.example'))->document()) === 'discovery_incomplete', 'oidc: incomplete discovery refused');

// --- ID token claims --------------------------------------------------------
$now = 1_800_000_000;
$good = ['iss' => 'https://central.example/oidc', 'aud' => 'scheduled-client', 'exp' => $now + 300, 'iat' => $now - 5, 'nonce' => 'n1', 'sub' => 'user-1'];
$idCheck = fn (array $c, string $nonce = 'n1') => IdTokenClaims::assertValid($c, 'https://central.example/oidc', 'scheduled-client', $nonce, $now);
$idCheck($good);
check(true, 'id token: valid claims accepted');
$idCheck(array_merge($good, ['aud' => ['other', 'scheduled-client']]));
check(true, 'id token: audience list accepted');
check(reasonOf(fn () => $idCheck(array_merge($good, ['iss' => 'https://evil.example/oidc']))) === 'wrong_issuer', 'id token: wrong issuer refused');
check(reasonOf(fn () => $idCheck(array_merge($good, ['aud' => 'sorted-client']))) === 'wrong_audience', 'id token: wrong audience refused');
check(reasonOf(fn () => $idCheck(array_merge($good, ['exp' => $now - 61]))) === 'expired', 'id token: expired refused');
check(reasonOf(fn () => $idCheck($good, 'other-nonce')) === 'nonce_mismatch', 'id token: state/nonce mismatch refused');
check(reasonOf(fn () => $idCheck(array_diff_key($good, ['sub' => 1]))) === 'missing_subject', 'id token: missing subject refused');

// --- Service token client ---------------------------------------------------
$svcUrl = 'https://central.example/oidc/token';
$accessFor = fn (string $aud, int $exp) => jwt(['aud' => $aud, 'exp' => $exp, 'sub' => 'svc']);
$t = 1_000;
$clock = function () use (&$t): int { return $t; };
$http = new FakeHttp([
    ['status' => 200, 'json' => discoveryDoc()],
    ['status' => 200, 'json' => ['access_token' => $accessFor('urn:titan-central:product:scheduled', 1300), 'expires_in' => 300]],
    ['status' => 200, 'json' => ['access_token' => $accessFor('urn:titan-central:product:scheduled', 1000 + 2000), 'expires_in' => 2000]],
]);
$discovery = new OidcDiscovery($http, 'https://central.example/oidc/.well-known/openid-configuration', 'https://central.example');
$svc = new ServiceTokenClient($http, $discovery, 'https://central.example', 'scheduled-svc', 'svc-secret-value', 'urn:titan-central:product:scheduled', false, $clock);
$first = $svc->accessToken();
$second = $svc->accessToken();
check($first === $second && count(array_filter($http->calls, fn ($c) => $c[0] === 'POST')) === 1, 'service: token cached');
check(str_contains(implode(' ', $http->calls[1][2]), 'Authorization: Basic '), 'service: client credentials sent as basic auth');
$t = 1_000 + 300 - 30; // inside the 60 s refresh margin
$svc->accessToken();
check(count(array_filter($http->calls, fn ($c) => $c[0] === 'POST')) === 2, 'service: refreshed before expiry');

$wrong = new FakeHttp([
    ['status' => 200, 'json' => discoveryDoc()],
    ['status' => 200, 'json' => ['access_token' => $accessFor('urn:titan-central:product:sorted', 2000), 'expires_in' => 300]],
]);
$wrongSvc = new ServiceTokenClient($wrong, new OidcDiscovery($wrong, 'https://central.example/d', 'https://central.example'), 'https://central.example', 'scheduled-svc', 'svc-secret-value', 'urn:titan-central:product:scheduled', false, fn () => 1_000);
check(reasonOf(fn () => $wrongSvc->accessToken()) === 'wrong_audience', 'service: token for another product refused');
$opaque = new FakeHttp([
    ['status' => 200, 'json' => discoveryDoc()],
    ['status' => 200, 'json' => ['access_token' => 'opaque-token', 'expires_in' => 300]],
]);
$opaqueSvc = new ServiceTokenClient($opaque, new OidcDiscovery($opaque, 'https://central.example/d', 'https://central.example'), 'https://central.example', 'scheduled-svc', 'svc-secret-value', 'urn:titan-central:product:scheduled', false, fn () => 1_000);
check(reasonOf(fn () => $opaqueSvc->accessToken()) === 'token_not_jwt', 'service: opaque token refused');
$failing = new FakeHttp([
    ['status' => 200, 'json' => discoveryDoc()],
    ['status' => 401, 'json' => ['error' => 'invalid_client', 'secret' => 'svc-secret-value']],
]);
$failingSvc = new ServiceTokenClient($failing, new OidcDiscovery($failing, 'https://central.example/d', 'https://central.example'), 'https://central.example', 'scheduled-svc', 'svc-secret-value', 'urn:titan-central:product:scheduled', false, fn () => 1_000);
$msg = '';
try {
    $failingSvc->accessToken();
} catch (CentralException $ex) {
    $msg = $ex->getMessage();
}
check($msg !== '' && !str_contains($msg, 'svc-secret-value'), 'service: failure message has no secret');
check(reasonOf(fn () => $svc->getJson('https://central.example/v1/x?organisation_id=abc')) === 'tenant_param_forbidden', 'service: tenant in query refused before send');
check(reasonOf(fn () => ServiceTokenClient::assertNoTenantParams(['filter' => ['cohort' => 'pilot']])) === 'tenant_param_forbidden', 'service: nested cohort in body refused');
ServiceTokenClient::assertNoTenantParams(['flag' => 'batch_size', 'environment' => 'staging']);
check(true, 'service: clean params accepted');

// --- Feature flags ----------------------------------------------------------
$decl = [
    'sample_flag' => ['type' => 'boolean', 'default' => false],
    'plan_tier' => ['type' => 'enum', 'default' => 'basic', 'allowed' => ['basic', 'pro']],
    'batch_size' => ['type' => 'integer', 'default' => 20, 'min' => 1, 'max' => 100],
];
$flags = new FlagReader($decl);
check($flags->read('sample_flag') === false && $flags->read('plan_tier') === 'basic' && $flags->read('batch_size') === 20, 'flags: declared defaults with no remote');
$answer = fn (array $a) => new FlagReader($decl, static fn (string $k) => $a);
check((new FlagReader($decl, static function (string $k): never { throw new RuntimeException('down'); }))->read('batch_size') === 20, 'flags: remote error returns default');
check($answer(['value' => true, 'source' => 'fallback'])->read('sample_flag') === false, 'flags: fallback answer returns default');
check($answer(['value' => 'enterprise', 'source' => 'global'])->read('plan_tier') === 'basic', 'flags: value outside enum returns default');
check($answer(['value' => 500, 'source' => 'global'])->read('batch_size') === 20, 'flags: out-of-range integer returns default');
check($answer(['value' => true, 'source' => 'override'])->read('sample_flag') === true, 'flags: valid remote value used');
check(reasonOf(fn () => $flags->read('not_declared')) === 'undeclared_flag', 'flags: undeclared flag refused');
check(reasonOf(fn () => new FlagReader(['x' => ['type' => 'boolean', 'default' => 'yes']])) === 'flag_declaration_invalid', 'flags: bad declared default refused');

// --- Signed events ----------------------------------------------------------
$secret = 'scheduled-test-only-secret-0123456789';
// Produced by Node with Central's canonicalJson (packages/control-plane/src/outbox.ts) and HMAC-SHA256.
$fixture = '{"eventId":"8f14e45f-ceea-4672-9c2a-1d2b3c4d5e6f","organisationId":"6f1d2c3b-4a59-4e6f-8a7b-9c0d1e2f3a4b","sourceProduct":"sorted","eventType":"example","schemaVersion":1,"correlationId":"corr-1","idempotencyKey":"example-1","occurredAt":"2026-11-15T00:00:00Z","payload":{"b":1,"a":{"y":[2,"x/é"],"x":true}},"signature":"5688b3d0b3e506472d5406f80b499ba6a3ad1e3515ccf6afb40ad3d007a6932a"}';
$verifier = new SignedEventVerifier($secret);
$nowOk = strtotime('2026-11-15T00:10:00Z');
check($verifier->verify($fixture, $nowOk)['eventType'] === 'example', 'events: Central-signed fixture verifies');
check(reasonOf(fn () => $verifier->verify(str_replace('"b":1', '"b":2', $fixture), $nowOk)) === 'bad_signature', 'events: tampered payload rejected');
check(reasonOf(fn () => (new SignedEventVerifier(str_repeat('z', 32)))->verify($fixture, $nowOk)) === 'bad_signature', 'events: wrong secret rejected');
check(reasonOf(fn () => $verifier->verify(preg_replace('/,"signature":"[0-9a-f]+"/', '', $fixture), $nowOk)) === 'unsigned', 'events: unsigned rejected');
check(reasonOf(fn () => $verifier->verify($fixture, strtotime('2026-11-17T00:00:00Z'))) === 'stale', 'events: stale rejected');
check(reasonOf(fn () => $verifier->verify($fixture, strtotime('2026-11-14T00:00:00Z'))) === 'from_future', 'events: future-dated rejected');
check(reasonOf(fn () => $verifier->verify(str_repeat(' ', 16385), $nowOk)) === 'body_too_large', 'events: oversized body rejected');

$base = [
    'eventId' => '8f14e45f-ceea-4672-9c2a-1d2b3c4d5e6f', 'organisationId' => '6f1d2c3b-4a59-4e6f-8a7b-9c0d1e2f3a4b',
    'sourceProduct' => 'sorted', 'eventType' => 'example', 'schemaVersion' => 1, 'correlationId' => 'c1',
    'idempotencyKey' => 'k-1', 'occurredAt' => '2026-11-15T00:00:00Z', 'payload' => ['n' => 1],
];
check(reasonOf(fn () => $verifier->verify(signedBody(array_merge($base, ['payload' => ['big' => str_repeat('x', 5000)]]), $secret), $nowOk)) === 'payload_too_large', 'events: oversized payload rejected');

$calls = 0;
$fail = true;
$consumer = new EventConsumer($verifier, new InMemoryProcessedEventStore(), 'scheduled', [
    'example.v1' => function (array $env) use (&$calls, &$fail): void {
        if ($fail) {
            $fail = false;
            throw new RuntimeException('effect failed');
        }
        $calls++;
    },
]);
$body = signedBody($base, $secret);
$threw = false;
try {
    $consumer->handle($body, $nowOk);
} catch (RuntimeException) {
    $threw = true;
}
check($threw && $calls === 0, 'consumer: failed effect propagates and runs nothing');
check($consumer->handle($body, $nowOk) === 'processed' && $calls === 1, 'consumer: retry after failure processes once');
check($consumer->handle($body, $nowOk) === 'duplicate' && $calls === 1, 'consumer: duplicate returns without effect');
check(reasonOf(fn () => $consumer->handle(signedBody(array_merge($base, ['schemaVersion' => 2]), $secret), $nowOk)) === 'unsupported_event', 'consumer: unsupported version refused');
check(reasonOf(fn () => $consumer->handle(str_replace('"n":1', '"n":9', $body), $nowOk)) === 'bad_signature' && $calls === 1, 'consumer: tampered event never reaches the handler');


// --- Central sign-in (flag-gated, EdDSA ID token, back channel) -------------
function signJws(array $header, array $payload, string $secretKey): string
{
    $h = b64url(json_encode($header));
    $p = b64url(json_encode($payload));

    return $h.'.'.$p.'.'.b64url(sodium_crypto_sign_detached($h.'.'.$p, $secretKey));
}
function jwksFor(string $publicKey, string $kid = 'k1'): array
{
    return ['keys' => [['kty' => 'OKP', 'crv' => 'Ed25519', 'x' => b64url($publicKey), 'kid' => $kid]]];
}
function signInFor(array $responses, int $now): array
{
    $http = new FakeHttp($responses);
    $discovery = new OidcDiscovery($http, 'https://central.example/oidc/.well-known/openid-configuration', 'https://central.example');
    $svc = new CentralSignIn($http, $discovery, 'scheduled-client', 'client-secret-value', 'https://scheduled.example/auth/titan/callback', 'scheduled-client', fn () => $now);

    return [$svc, $http];
}
$sNow = 1_800_000_000;
$kp = sodium_crypto_sign_keypair();
$sk = sodium_crypto_sign_secretkey($kp);
$pk = sodium_crypto_sign_publickey($kp);
$otherPk = sodium_crypto_sign_publickey(sodium_crypto_sign_keypair());
$claimsBase = ['iss' => 'https://central.example/oidc', 'aud' => 'scheduled-client', 'exp' => $sNow + 300, 'iat' => $sNow - 5, 'nonce' => 'n1', 'sub' => 'user-1'];
$header = ['alg' => 'EdDSA', 'kid' => 'k1', 'typ' => 'JWT'];
$goodIdToken = signJws($header, $claimsBase, $sk);
$pending = ['state' => 'st1', 'nonce' => 'n1', 'code_verifier' => str_repeat('v', 43)];
$discResp = ['status' => 200, 'json' => discoveryDoc()];
$tokenOk = fn (string $idToken) => ['status' => 200, 'json' => ['id_token' => $idToken, 'token_type' => 'Bearer']];
$jwksOk = ['status' => 200, 'json' => jwksFor($pk)];

// Flag: off unless exactly on.
check(envFrom([])->signInEnabled() === false, 'signin flag: unset is off');
check(envFrom(['CENTRAL_SIGNIN_ENABLED' => '1'])->signInEnabled() === true, 'signin flag: 1 is on');
check(envFrom(['CENTRAL_SIGNIN_ENABLED' => 'TRUE'])->signInEnabled() === true, 'signin flag: TRUE is on');
check(envFrom(['CENTRAL_SIGNIN_ENABLED' => 'yes'])->signInEnabled() === false && envFrom(['CENTRAL_SIGNIN_ENABLED' => ''])->signInEnabled() === false, 'signin flag: other values stay off');

// ID token signature (EdDSA, Central JWKS).
check(IdTokenVerifier::verify($goodIdToken, jwksFor($pk))['sub'] === 'user-1', 'id sig: valid EdDSA token verifies');
$tampered = explode('.', $goodIdToken);
$tampered[1] = b64url(json_encode(array_merge($claimsBase, ['sub' => 'user-2'])));
check(reasonOf(fn () => IdTokenVerifier::verify(implode('.', $tampered), jwksFor($pk))) === 'bad_signature', 'id sig: tampered payload refused');
check(reasonOf(fn () => IdTokenVerifier::verify(signJws($header, $claimsBase, $sk), jwksFor($otherPk))) === 'bad_signature', 'id sig: wrong key refused');
check(reasonOf(fn () => IdTokenVerifier::verify(signJws(['alg' => 'HS256', 'kid' => 'k1'], $claimsBase, $sk), jwksFor($pk))) === 'bad_algorithm', 'id sig: HS256 refused');
check(reasonOf(fn () => IdTokenVerifier::verify(b64url('{"alg":"none"}').'.'.b64url(json_encode($claimsBase)).'.', jwksFor($pk))) === 'token_malformed', 'id sig: alg none with empty signature refused');
check(reasonOf(fn () => IdTokenVerifier::verify(signJws(['alg' => 'none'], $claimsBase, $sk), jwksFor($pk))) === 'bad_algorithm', 'id sig: alg none refused');
check(reasonOf(fn () => IdTokenVerifier::verify($goodIdToken, jwksFor($pk, 'other-kid'))) === 'unknown_key', 'id sig: unknown kid refused');
check(reasonOf(fn () => IdTokenVerifier::verify($goodIdToken, ['keys' => [['kty' => 'RSA', 'n' => 'x', 'e' => 'AQAB']]])) === 'unknown_key', 'id sig: non-OKP key ignored');
check(reasonOf(fn () => IdTokenVerifier::verify('a.b', jwksFor($pk))) === 'token_malformed', 'id sig: two-part token refused');
check(reasonOf(fn () => IdTokenVerifier::verify(str_repeat('a', 9000), jwksFor($pk))) === 'token_malformed', 'id sig: oversized token refused');

// Full back-channel sign-in.
[$svc, $http] = signInFor([$discResp, $tokenOk($goodIdToken), $jwksOk], $sNow);
$verified = $svc->complete($pending, 'st1', 'auth-code-value');
check($verified === ['iss' => 'https://central.example/oidc', 'sub' => 'user-1'], 'signin: verified issuer and subject returned');
check($http->calls[1][0] === 'POST' && $http->calls[1][1] === 'https://central.example/oidc/token', 'signin: code exchanged at token endpoint over POST');
check(str_starts_with($http->calls[1][2][0] ?? '', 'Authorization: Basic '), 'signin: client authenticated with basic auth');

[$svc, $http] = signInFor([], $sNow);
check(reasonOf(fn () => $svc->complete($pending, 'other-state', 'code')) === 'state_mismatch' && count($http->calls) === 0, 'signin: state mismatch refused before any request');
[$svc] = signInFor([], $sNow);
check(reasonOf(fn () => $svc->complete($pending, null, 'code')) === 'state_mismatch', 'signin: missing returned state refused');
check(reasonOf(fn () => $svc->complete([], 'st1', 'code')) === 'no_pending_signin', 'signin: no pending sign-in refused');
check(reasonOf(fn () => $svc->complete($pending, 'st1', null)) === 'code_missing', 'signin: missing code refused');
check(reasonOf(fn () => $svc->complete($pending, 'st1', ['array'])) === 'code_missing', 'signin: array code refused');

[$svc] = signInFor([$discResp, ['status' => 400, 'json' => ['error' => 'invalid_grant', 'code' => 'secret-code']]], $sNow);
check(reasonOf(fn () => $svc->complete($pending, 'st1', 'bad-code')) === 'token_exchange_failed', 'signin: failed token exchange refused');

[$svc] = signInFor([$discResp, $tokenOk(signJws($header, array_merge($claimsBase, ['nonce' => 'other']), $sk)), $jwksOk], $sNow);
check(reasonOf(fn () => $svc->complete($pending, 'st1', 'code')) === 'nonce_mismatch', 'signin: nonce mismatch refused');

[$svc] = signInFor([$discResp, $tokenOk(signJws($header, array_merge($claimsBase, ['exp' => $sNow - 100]), $sk)), $jwksOk], $sNow);
check(reasonOf(fn () => $svc->complete($pending, 'st1', 'code')) === 'expired', 'signin: expired ID token refused');

[$svc] = signInFor([$discResp, $tokenOk(signJws($header, array_merge($claimsBase, ['iss' => 'https://evil.example/oidc']), $sk)), $jwksOk], $sNow);
check(reasonOf(fn () => $svc->complete($pending, 'st1', 'code')) === 'wrong_issuer', 'signin: wrong issuer refused');

[$svc] = signInFor([$discResp, $tokenOk(signJws($header, array_merge($claimsBase, ['aud' => ['other', 'scheduled-client']]), $sk)), $jwksOk], $sNow);
check(reasonOf(fn () => $svc->complete($pending, 'st1', 'code')) === 'wrong_audience', 'signin: multi-audience token without azp refused');

[$svc] = signInFor([$discResp, $tokenOk(signJws($header, array_merge($claimsBase, ['aud' => 'sorted-client']), $sk)), $jwksOk], $sNow);
check(reasonOf(fn () => $svc->complete($pending, 'st1', 'code')) === 'wrong_audience', 'signin: token for another client refused');

[$svc] = signInFor([$discResp, $tokenOk($goodIdToken), ['status' => 503, 'json' => null]], $sNow);
check(reasonOf(fn () => $svc->complete($pending, 'st1', 'code')) === 'jwks_failed', 'signin: unreadable JWKS refused');

// Link resolution: no existing link means no local user.
check((new NoExistingLinkResolver())->localUserId('https://central.example/oidc', 'user-1') === null, 'link: no existing link maps to no local user');

// Wiring: routes, controller and gating (source contract, no Laravel boot needed).
$webRoutes = (string) file_get_contents(dirname(__DIR__).'/core/routes/web.php');
$controller = (string) file_get_contents(dirname(__DIR__).'/core/app/Http/Controllers/Central/CentralSignInController.php');
check(str_contains($webRoutes, "'prefix' => 'auth/titan'") && str_contains($webRoutes, "CentralSignInController@callback"), 'wiring: central routes mounted');
check(str_contains($webRoutes, "Route::get('/login/google', 'User\\LoginController@redirectToGoogle'") && str_contains($webRoutes, "Route::get('/login/facebook/callback', 'User\\LoginController@handleFacebookCallback'"), 'wiring: local, Google and Facebook login routes unchanged');
check(str_contains($controller, 'signInEnabled()') && str_contains($controller, 'abort(404)'), 'wiring: every Central action gated by flag');
check(!str_contains($controller, 'User::create') && !str_contains($controller, 'create(') && !str_contains($controller, "guard('admin')") && !str_contains($controller, 'Admin\\'), 'wiring: no user creation and no admin guard');
// --- Inbound event route: tenant, replay, freshness, flag, store -------------
use App\Central\Events\DatabaseProcessedEventStore;
use App\Central\Events\EventIngress;

$orgId = '6f1d2c3b-4a59-4e6f-8a7b-9c0d1e2f3a4b';
$otherOrg = '1a2b3c4d-5e6f-4a7b-8c9d-0e1f2a3b4c5d';
check(envFrom(['CENTRAL_EVENTS_ORGANISATION_ID' => strtoupper($orgId)])->eventsOrganisationId() === $orgId, 'config: organisation id normalised to lowercase');
check(reasonOf(fn () => envFrom(['CENTRAL_EVENTS_ORGANISATION_ID' => 'not-a-uuid'])->eventsOrganisationId()) === 'config_invalid', 'config: organisation id must be a UUID');
check(reasonOf(fn () => envFrom([])->eventsOrganisationId()) === 'config_missing', 'config: organisation id required');
check(envFrom([])->eventsEnabled() === false && envFrom(['CENTRAL_EVENTS_ENABLED' => 'yes'])->eventsEnabled() === false, 'config: events flag off by default and for non-truthy values');
check(envFrom(['CENTRAL_EVENTS_ENABLED' => '1'])->eventsEnabled() && envFrom(['CENTRAL_EVENTS_ENABLED' => 'TRUE'])->eventsEnabled(), 'config: events flag on for 1 and true');

$handled = [];
$handler = function (array $env) use (&$handled): void {
    $handled[] = $env['eventId'];
};
$makeConsumer = fn (?string $org, array $handlers) => new EventConsumer(
    new SignedEventVerifier($secret), new InMemoryProcessedEventStore(), 'scheduled', $handlers, $org
);
$validBody = signedBody($base, $secret);

// Tenant: a correctly signed event for another organisation is refused and never claimed or handled.
$tenant = $makeConsumer($orgId, ['example.v1' => $handler]);
$foreign = signedBody(array_merge($base, ['organisationId' => $otherOrg]), $secret);
check(reasonOf(fn () => $tenant->handle($foreign, $nowOk)) === 'tenant_mismatch' && $handled === [], 'tenant: signed event for another organisation refused');
// Order: a foreign org with a bad signature must fail on the signature, not on the tenant.
$forgedForeign = str_replace('"n":1', '"n":2', $foreign);
check(reasonOf(fn () => $tenant->handle($forgedForeign, $nowOk)) === 'bad_signature', 'tenant: signature is checked before the tenant');
check($tenant->handle($validBody, $nowOk) === 'processed' && count($handled) === 1, 'tenant: event for the configured organisation processed');

// Replay: the same delivery twice runs the handler once.
$replay = $makeConsumer($orgId, ['example.v1' => $handler]);
check($replay->handle($validBody, $nowOk) === 'processed' && $replay->handle($validBody, $nowOk) === 'duplicate' && count($handled) === 2, 'replay: second delivery is a duplicate and runs nothing');

// Ingress: HTTP status mapping and the flag.
$ingressOn = new EventIngress($makeConsumer($orgId, ['example.v1' => $handler]), true);
$ingressOff = new EventIngress($makeConsumer($orgId, ['example.v1' => $handler]), false);
check($ingressOff->handle($validBody, $nowOk) === [404, ['error' => 'not_found']] && count($handled) === 2, 'ingress: disabled flag answers 404 and handles nothing');
[$status, $body] = $ingressOn->handle(str_replace('"n":1', '"n":9', $validBody), $nowOk);
check($status === 401 && $body === ['error' => 'bad_signature'], 'ingress: bad signature answers 401');
$before = count($handled);
[$status, $body] = $ingressOn->handle($validBody, $nowOk);
check($status === 200 && $body === ['status' => 'processed'] && count($handled) === $before + 1, 'ingress: valid event answers 200 processed');
[$status, $body] = $ingressOn->handle($validBody, $nowOk);
check($status === 200 && $body === ['status' => 'duplicate'] && count($handled) === $before + 1, 'ingress: replayed valid event answers 200 duplicate');
$fresh = signedBody(array_merge($base, ['eventId' => '9a8b7c6d-5e4f-4a3b-8c2d-1e0f9a8b7c6d', 'idempotencyKey' => 'k-future']), $secret);
$before = count($handled);
[$status, $body] = $ingressOn->handle($fresh, $nowOk);
check($status === 200 && $body === ['status' => 'processed'] && count($handled) === $before + 1, 'ingress: new event with a fresh key answers 200 processed');
$future = signedBody(array_merge($base, ['eventId' => '9a8b7c6d-5e4f-4a3b-8c2d-1e0f9a8b7c6e', 'idempotencyKey' => 'k-far-future']), $secret);
$before = count($handled);
[$status, $body] = $ingressOn->handle($future, strtotime('2026-11-14T23:00:00Z'));
check($status === 400 && $body === ['error' => 'from_future'] && count($handled) === $before, 'ingress: future-dated event answers 400 and is not handled');
[$status] = $ingressOn->handle(signedBody(array_merge($base, ['schemaVersion' => 2, 'idempotencyKey' => 'k-v2']), $secret), $nowOk);
check($status === 422, 'ingress: unsupported event version answers 422');
$failing = new EventIngress($makeConsumer($orgId, ['example.v1' => function (): void {
    throw new RuntimeException('effect failed');
}]), true);
[$status, $body] = $failing->handle(signedBody(array_merge($base, ['idempotencyKey' => 'k-fail']), $secret), $nowOk);
check($status === 500 && $body === ['error' => 'handler_failed'], 'ingress: handler failure answers 500 so Central retries');

// Database store: claim, duplicate, release. SQLite stands in for the migration's table here.
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE TABLE central_processed_events (id INTEGER PRIMARY KEY AUTOINCREMENT, consumer VARCHAR(64) NOT NULL, idempotency_key VARCHAR(200) NOT NULL, event_id VARCHAR(36) NOT NULL, created_at TIMESTAMP NOT NULL, UNIQUE (consumer, idempotency_key))');
$store = new DatabaseProcessedEventStore($pdo);
$eid = '8f14e45f-ceea-4672-9c2a-1d2b3c4d5e6f';
check($store->claim('scheduled', 'key-a', $eid) === true, 'db store: first claim succeeds');
check($store->claim('scheduled', 'key-a', $eid) === false, 'db store: duplicate claim refused by unique key');
check($store->claim('other', 'key-a', $eid) === true, 'db store: same key under another consumer is independent');
$store->release('scheduled', 'key-a');
check($store->claim('scheduled', 'key-a', $eid) === true, 'db store: released claim can be taken again');
$durable = new DatabaseProcessedEventStore($pdo);
check($durable->claim('scheduled', 'key-a', $eid) === false, 'db store: claim survives a new store instance');
$dbConsumer = new EventConsumer(new SignedEventVerifier($secret), $store, 'scheduled', ['example.v1' => $handler], $orgId);
$dbBody = signedBody(array_merge($base, ['idempotencyKey' => 'k-db']), $secret);
check($dbConsumer->handle($dbBody, $nowOk) === 'processed' && $dbConsumer->handle($dbBody, $nowOk) === 'duplicate', 'db store: consumer replay protection through the database');

// Claim and effect share one transaction: a failed effect leaves no claim and no write, so the retry is processed.
$atomicPdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$atomicPdo->exec('CREATE TABLE central_processed_events (id INTEGER PRIMARY KEY AUTOINCREMENT, consumer VARCHAR(64) NOT NULL, idempotency_key VARCHAR(200) NOT NULL, event_id VARCHAR(36) NOT NULL, created_at TIMESTAMP NOT NULL, UNIQUE (consumer, idempotency_key))');
$atomicPdo->exec('CREATE TABLE side_effects (id INTEGER PRIMARY KEY AUTOINCREMENT, label VARCHAR(64) NOT NULL)');
$effectFails = true;
$atomicConsumer = new EventConsumer(new SignedEventVerifier($secret), new DatabaseProcessedEventStore($atomicPdo), 'scheduled', [
    'example.v1' => function (array $env) use ($atomicPdo, &$effectFails): void {
        $atomicPdo->prepare('INSERT INTO side_effects (label) VALUES (?)')->execute([$env['eventId']]);
        if ($effectFails) {
            $effectFails = false;
            throw new RuntimeException('effect failed after its write');
        }
    },
], $orgId);
$atomicBody = signedBody(array_merge($base, ['idempotencyKey' => 'k-atomic']), $secret);
$atomicThrew = false;
try {
    $atomicConsumer->handle($atomicBody, $nowOk);
} catch (RuntimeException) {
    $atomicThrew = true;
}
$rowCount = static fn (string $table): int => (int) $atomicPdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
check($atomicThrew && $rowCount('central_processed_events') === 0 && $rowCount('side_effects') === 0 && !$atomicPdo->inTransaction(), 'db store: failed effect rolls back its claim and its write');
check($atomicConsumer->handle($atomicBody, $nowOk) === 'processed' && $rowCount('side_effects') === 1, 'db store: retry after a failed effect is processed, not reported as duplicate');
check($atomicConsumer->handle($atomicBody, $nowOk) === 'duplicate' && $rowCount('side_effects') === 1, 'db store: effect applied once, replay is duplicate');

// Narrowed duplicate check: a non-duplicate integrity failure (SQLSTATE 23000) must surface as an error.
$strictPdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$strictPdo->exec('CREATE TABLE central_processed_events (id INTEGER PRIMARY KEY AUTOINCREMENT, consumer VARCHAR(64) NOT NULL, idempotency_key VARCHAR(200) NOT NULL, event_id VARCHAR(36) NOT NULL, created_at TIMESTAMP NOT NULL, UNIQUE (consumer, idempotency_key))');
$strictPdo->exec("CREATE TRIGGER block_consumer BEFORE INSERT ON central_processed_events WHEN NEW.consumer = 'blocked' BEGIN SELECT RAISE(ABORT, 'consumer blocked'); END");
$strictStore = new DatabaseProcessedEventStore($strictPdo);
$integrityError = null;
try {
    $strictStore->claim('blocked', 'k-x', $eid);
} catch (PDOException $e) {
    $integrityError = $e;
}
check($integrityError !== null && $integrityError->getCode() === '23000', 'db store: non-duplicate integrity failure is thrown, not reported as duplicate');
$blockedRun = false;
$blockedThrew = false;
try {
    $strictStore->claimAndRun('blocked', 'k-x', $eid, function () use (&$blockedRun): void {
        $blockedRun = true;
    });
} catch (PDOException) {
    $blockedThrew = true;
}
check($blockedThrew && !$blockedRun, 'db store: effect never runs when the claim fails for a non-duplicate reason');
check($strictStore->claim('scheduled', 'k-x', $eid) === true && $strictStore->claim('scheduled', 'k-x', $eid) === false, 'db store: a real duplicate key still returns false');
// --- Env template: every variable CentralConfig reads must be declared in core/.env.example ---
$configSource = (string) file_get_contents(dirname(__DIR__).'/core/app/Central/Config/CentralConfig.php');
$template = (string) file_get_contents(dirname(__DIR__).'/core/.env.example');
preg_match_all("/(?:required|lookup)\\(\\s*'([A-Z][A-Z0-9_]*)'\\s*\\)/", $configSource, $readMatches);
$readNames = array_values(array_unique($readMatches[1]));
preg_match_all('/^([A-Z][A-Z0-9_]*)=/m', $template, $templateMatches);
$declared = $templateMatches[1];
check(count($readNames) >= 9, 'env template: CentralConfig read set was found by the scan');
foreach ($readNames as $name) {
    check(in_array($name, $declared, true), "env template: {$name} is read by CentralConfig but missing from core/.env.example");
}

require_once __DIR__.'/booking_event_outbox_test.php';

// --- Report -----------------------------------------------------------------
$total = $passed + count($failures);
foreach ($failures as $label) {
    fwrite(STDERR, "FAIL: {$label}\n");
}
echo "{$passed}/{$total} Central integration checks passed\n";
exit($failures === [] ? 0 : 1);
