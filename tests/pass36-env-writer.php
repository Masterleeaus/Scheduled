<?php
require __DIR__ . '/../core/app/Support/Install/EnvironmentPatchWriter.php';
use App\Support\Install\EnvironmentPatchWriter;

$key = 'base64:' . base64_encode(str_repeat('K', 32));
$path = tempnam(sys_get_temp_dir(), 'installer-pass36-');
file_put_contents($path, "APP_KEY=$key\nAPP_DEBUG=true\nDB_PASSWORD=old\nDB_PASSWORD=stale\nTITAN_BOOKING_SIGNING_KEY=preserve-this\nCUSTOM_PROVIDER=enabled\n");
$tests = 0;
$assert = function ($truth, $description) use (&$tests) {
    if (!$truth) { throw new RuntimeException('FAILED: ' . $description); }
    ++$tests;
};
try {
    EnvironmentPatchWriter::apply($path, [
        'APP_KEY' => $key, 'APP_DEBUG' => 'false', 'DB_PASSWORD' => 'safe password#42',
        'DB_DATABASE' => 'fresh_install', 'APP_NAME' => 'My Business',
    ]);
    $new = file_get_contents($path);
    $assert(str_contains($new, 'TITAN_BOOKING_SIGNING_KEY=preserve-this'), 'preserve booking integration secrets');
    $assert(str_contains($new, 'CUSTOM_PROVIDER=enabled'), 'preserve unknown existing settings');
    $assert(substr_count($new, 'DB_PASSWORD=') === 1, 'remove duplicate overwritten keys');
    $assert(str_contains($new, 'DB_PASSWORD="safe password#42"'), 'quote special-character password');
    $assert(str_contains($new, 'APP_DEBUG=false'), 'force production debug setting');
    $assert(str_contains($new, 'DB_DATABASE=fresh_install'), 'append missing database configuration');
    $assert(str_contains($new, 'APP_KEY=' . $key), 'keep existing session key');
    try { EnvironmentPatchWriter::apply($path, ['DB_PASSWORD' => "bad\nINJECTED=true"]); $assert(false, 'reject newline'); }
    catch (InvalidArgumentException $e) { $assert(true, 'reject newline'); }
    $assert(!str_contains(file_get_contents($path), 'INJECTED=true'), 'no changes on invalid input');
    try { EnvironmentPatchWriter::apply($path, ['APP_KEY' => 'base64:' . base64_encode(str_repeat('Z', 32))]); $assert(false, 'reject key rotation'); }
    catch (RuntimeException $e) { $assert(true, 'reject key rotation'); }
    $assert(str_contains(file_get_contents($path), 'APP_KEY=' . $key), 'key still unchanged on rejection');
    echo "PASS36 environment writer: {$tests}/{$tests} assertions passed\n";
} finally {
    @unlink($path); @unlink($path . '.install.lock');
}
