<?php
/** Run: php scripts/preflight.php [--json] ; no writes and no secrets printed. */
$root = dirname(__DIR__);
$core = $root . '/core';
$checks = [];
$add = static function (string $name, bool $ok, string $detail = '') use (&$checks): void {
    $checks[] = ['check' => $name, 'pass' => $ok, 'detail' => $detail];
};
$add('PHP >= 8.1', version_compare(PHP_VERSION, '8.1.0', '>='), PHP_VERSION);
foreach (['pdo','pdo_pgsql','openssl','mbstring','tokenizer','json','curl','fileinfo','exif','gmp'] as $ext) {
    $add('extension '.$ext, extension_loaded($ext));
}
foreach (['index.php','core/artisan','core/vendor/autoload.php','core/bootstrap/app.php','installer/database.sql','private/cleanto/ct_install.php'] as $file) {
    $add('required '.$file, is_file($root.'/'.$file));
}
foreach (['core/storage','core/storage/framework','core/storage/logs','core/bootstrap/cache','assets/admin/img','assets/front/img'] as $dir) {
    $add('writable '.$dir, is_dir($root.'/'.$dir) && is_writable($root.'/'.$dir));
}
$env = $core.'/.env';
$add('environment configured', is_file($env), 'core/.env should be installed securely');
if (is_file($env)) {
    $config = @file_get_contents($env) ?: '';
    $add('debug disabled', preg_match('/^APP_DEBUG=false\s*$/mi', $config) === 1);
    $add('application key present', preg_match('/^APP_KEY=base64:[A-Za-z0-9+\/=]{40,}$/mi', $config) === 1);
    $add('production environment', preg_match('/^APP_ENV=production\s*$/mi', $config) === 1);
    // Fail closed when the old Laravel MySQL default or local login remains configured.
    $add('PostgreSQL connection selected', preg_match('/^DB_CONNECTION=pgsql\s*$/mi', $config) === 1);
    $add('Central OIDC sign-in enabled', preg_match('/^CENTRAL_SIGNIN_ENABLED=(true|1)\s*$/mi', $config) === 1);
}
$add('HTTP server forbids core access (manual verification)', false, 'Configure Apache/Nginx to block /core, /private and /integration; .htaccess is not sufficient for Nginx');
$add('shared PostgreSQL migration/booking acceptance (manual verification)', false, 'Requires coordinated Central migrations, two-company tenant isolation, booking concurrency and full end-to-end tests');
$add('payment and AI provider acceptance (manual verification)', false, 'Requires configured provider accounts');
$pass = count(array_filter($checks, fn($c) => $c['pass']));
if (in_array('--json', $argv, true)) {
    echo json_encode(['passed'=>$pass,'total'=>count($checks),'checks'=>$checks], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
} else {
    foreach ($checks as $check) {
        echo ($check['pass'] ? '[PASS] ' : '[BLOCK] ').$check['check'].($check['detail'] ? ': '.$check['detail'] : '').PHP_EOL;
    }
    echo "$pass/".count($checks)." installation readiness checks pass.\n";
}
exit($pass === count($checks) ? 0 : 1);
