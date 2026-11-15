<?php
/** Run: php scripts/preflight.php [--json] ; no writes and no secrets printed. */
$root = dirname(__DIR__);
$core = $root . '/core';
$checks = [];
$add = static function (string $name, bool $ok, string $detail = '') use (&$checks): void {
    $checks[] = ['check' => $name, 'pass' => $ok, 'detail' => $detail];
};
$add('PHP >= 8.1', version_compare(PHP_VERSION, '8.1.0', '>='), PHP_VERSION);
foreach (['mysqli','pdo','pdo_mysql','openssl','mbstring','tokenizer','json','curl','fileinfo','exif','gmp'] as $ext) {
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
}
$add('HTTP server forbids core access (manual verification)', false, 'Configure Apache/Nginx to block /core, /private and /integration; .htaccess is not sufficient for Nginx');
$add('MariaDB migration/booking acceptance (manual verification)', false, 'Requires real database, tenant and booking tests');
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
