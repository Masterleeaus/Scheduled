<?php
require __DIR__.'/../core/app/Support/Install/InstallationCompletionGuard.php';
use App\Support\Install\InstallationCompletionGuard as Guard;
$assertions = 0;
function check($condition, $label) { global $assertions; $assertions++; if (!$condition) { fwrite(STDERR, "FAIL: $label\n"); exit(1); } }
$ok = [
 'schema_imported'=>true, 'administrator_created'=>true, 'identity_schema_valid'=>true,
 'administrator_count'=>1, 'active_administrator_count'=>1, 'customer_count'=>0,
 'administrator_hash_present'=>true, 'app_key'=>'base64:'.base64_encode(random_bytes(32)),
 'environment_readable'=>true, 'storage_writable'=>true, 'installed_marker_exists'=>false,
];
check(Guard::problems($ok) === [], 'Valid fresh install accepted');
foreach (array_keys($ok) as $key) {
 $invalid = $ok;
 $invalid[$key] = $key === 'app_key' ? 'invalid' : ($key === 'customer_count' ? 1 : ($key === 'installed_marker_exists' ? true : ($key === 'administrator_count' || $key === 'active_administrator_count' ? 2 : false)));
 check(Guard::problems($invalid) !== [], 'Reject invalid '.$key);
}
foreach (['', 'base64:bad', 'base64:'.base64_encode(random_bytes(16)), 'plaintext'] as $invalid) {
 check(!Guard::keyIsValid($invalid), 'Reject bad application key');
}
check(Guard::keyIsValid($ok['app_key']), 'Accept strong key');
$controller = file_get_contents(__DIR__.'/../core/vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php');
$manager = file_get_contents(__DIR__.'/../core/vendor/rachidlaasri/laravel-installer/src/Helpers/FinalInstallManager.php');
check(strpos($controller, "Schema::hasTable('admins')") !== false, 'Final check schema');
check(strpos($controller, "'installer_schema_imported'") !== false, 'Final check import token');
check(strpos($controller, "'customer_count'") !== false, 'Final check seeded customers');
check(strpos($manager, "throw new \\RuntimeException") !== false, 'Final helper fail-closed');
check(strpos($manager, "Artisan::call") === false, 'No silent Artisan commands');
$adminController = file_get_contents(__DIR__.'/../core/vendor/rachidlaasri/laravel-installer/src/Controllers/AdministratorController.php');
check(strpos($adminController, "session()->forget('installer_schema_imported')") === false, 'Admin step retains schema token for finalisation');
echo "PASS {$assertions} installer completion assertions\n";
