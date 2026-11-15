<?php
require_once __DIR__.'/../core/app/Support/Install/InstallationDatabaseLock.php';
use App\Support\Install\InstallationDatabaseLock as Lock;
$checks=0;
function check($cond,$message) { global $checks; if(!$cond){fwrite(STDERR,"FAIL: $message\n");exit(1);} $checks++; }
check(Lock::name('db_a')===Lock::name('db_a'),'same database stable');
check(Lock::name('db_a')!==Lock::name('db_b'),'databases isolated');
check(strlen(Lock::name('db_a'))<64,'within MySQL named-lock limit');
check(preg_match('/^pa_install_[0-9a-f]{40}$/',Lock::name('db_a'))===1,'safe lock characters');
foreach(['',str_repeat('a',65)] as $invalid) {try {Lock::name($invalid); check(false,'invalid identity rejected');}catch(\RuntimeException $e){check(true,'invalid identity rejected');}}
$src=file_get_contents(__DIR__.'/../core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php');
$lock=strpos($src,'InstallationDatabaseLock::acquire($connection)');
$empty=strpos($src,'LegacySqlImporter::ensureEmptyDatabase($connection)');
$legacy=strpos($src,'LegacySqlImporter::import($connection');
$migrate=strpos($src,'InitialFeatureMigrations::apply(database_path');
$release=strpos($src,'InstallationDatabaseLock::release($connection)');
check($lock!==false && $lock<$empty,'lock before emptiness check');
check($lock<$legacy && $legacy<$migrate,'lock spans import and feature migrations');
check($release>$migrate,'release after all migrations');
check(strpos($src,'finally {', $migrate)!==false,'release in finally path');
check(strpos($src,'$connection->close();',$release)>$release,'connection closed after release');
echo "PASS: $checks installation lock and controller ordering checks\n";
