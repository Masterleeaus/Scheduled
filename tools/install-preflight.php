<?php
/** Run from the application root: php tools/install-preflight.php */
declare(strict_types=1);
$root = dirname(__DIR__);
$checks = [];
$record = static function (string $name, bool $ok, string $remedy = '') use (&$checks): void {
    $checks[] = ['check' => $name, 'passed' => $ok, 'remedy' => $ok ? '' : $remedy];
};
$record('PHP >= 8.0', PHP_VERSION_ID >= 80000, 'Upgrade to a supported PHP version.');
foreach (['pdo_mysql','mysqli','openssl','mbstring','curl','fileinfo','json','tokenizer'] as $extension) {
    $record('PHP extension '.$extension, extension_loaded($extension), 'Enable extension '.$extension.' in the web and CLI PHP environments.');
}
foreach (['core/vendor/autoload.php','core/.env.example','installer/database.sql','core/artisan','index.php'] as $relative) {
    $record('Required file '.$relative, is_file($root.'/'.$relative), 'Restore it from the verified master archive.');
}
foreach (['core/storage','core/bootstrap/cache'] as $relative) {
    $record('Writable directory '.$relative, is_dir($root.'/'.$relative) && is_writable($root.'/'.$relative), 'Create the directory and grant only the PHP service account write permission.');
}
$installed = is_file($root.'/core/storage/installed');
// Reject an archive without the public root protections. Nginx and other servers must
// independently enforce equivalent rules; .htaccess only applies when Apache allows it.
$publicRules = @file_get_contents($root.'/.htaccess');
$record('Apache private-source deny rules', is_string($publicRules)
    && strpos($publicRules, 'RewriteRule ^(?:core|tools|tests|docs|') !== false
    && strpos($publicRules, 'installer/database\.sql') !== false
    && strpos($publicRules, 'core|private|tools|tests|docs|integration|development|scripts|PASS05_EVIDENCE') !== false
    && strpos($publicRules, 'RECOVERY_STATUS_PASS') !== false,
    'Install the shipped root .htaccess and enable Apache AllowOverride FileInfo. For Nginx use docs/deployment/nginx-security.conf.');
$record('Bundled SQL not under an alternate public root', is_file($root.'/installer/database.sql')
    && is_file($root.'/index.php'),
    'Use the shipped document-root layout and deny access to installer/database.sql.');
$record('Fresh-install target', ! $installed, 'This is already marked as installed; use the verified upgrade workflow, not the fresh installer.');
$report = ['tool' => 'Pass 43 installation preflight', 'ready_for_attempt' => !in_array(false,array_column($checks,'passed'),true), 'checks' => $checks];
$asJson = in_array('--json', $argv, true);
if ($asJson) { echo json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n"; }
else { foreach ($checks as $item) { echo ($item['passed'] ? '[OK] ' : '[BLOCKED] ').$item['check'].($item['remedy'] ? ' — '.$item['remedy'] : '')."\n"; } }
exit($report['ready_for_attempt'] ? 0 : 1);
