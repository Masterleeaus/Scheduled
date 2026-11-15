<?php
require_once __DIR__ . '/../core/app/Support/Install/DatabaseEndpoint.php';
use App\Support\Install\DatabaseEndpoint;
$ok = 0;
function verify($condition, $reason) { global $ok; if (!$condition) { throw new RuntimeException($reason); } $ok++; }
foreach (['3306'=>3306, '1'=>1, '65535'=>65535, '13306'=>13306, 3307=>3307] as $raw=>$expected) {
    verify(DatabaseEndpoint::port((string)$raw) === $expected, 'Valid port rejected');
}
foreach (['', '0', '65536', '-1', '3306junk', '3306.0', ' 3306', '03306', '999999', '1e2'] as $bad) {
    try { DatabaseEndpoint::port($bad); throw new RuntimeException('Unsafe port was accepted: '.$bad); }
    catch (InvalidArgumentException $expected) { $ok++; }
}
$root = dirname(__DIR__);
$controller = file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php');
$manager = file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Helpers/EnvironmentManager.php');
$view = file_get_contents($root.'/core/resources/views/vendor/installer/environment-wizard.blade.php');
$config = file_get_contents($root.'/core/config/installer.php');
verify(str_contains($controller, "'port' => \App\Support\Install\DatabaseEndpoint::port"), 'PDO missing validated port');
verify(str_contains($controller, '$databasePort') && str_contains($controller, 'database_name'), 'mysqli missing port');
verify(str_contains($manager, "'DB_PORT' => (string) \App\Support\Install\DatabaseEndpoint::port"), '.env port not wired');
verify(str_contains($config, "'database_port'         => 'required|integer|between:1,65535'"), 'Validation missing');
verify(str_contains($view, 'name="database_port"') && str_contains($view, 'max="65535"'), 'Wizard port input missing');
echo "PASS55 MariaDB endpoint tests: $ok checks passed\n";
