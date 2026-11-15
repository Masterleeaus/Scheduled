<?php
$root = dirname(__DIR__);
$read = fn($p) => file_get_contents($root . '/' . $p);
$checks = [
 'configuration preserves existing env' => str_contains($read('core/vendor/rachidlaasri/laravel-installer/src/Helpers/EnvironmentManager.php'), 'EnvironmentPatchWriter::apply'),
 'db driver preflight' => str_contains($read('core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php'), "extension_loaded('pdo_mysql')") && str_contains($read('core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php'), "extension_loaded('mysqli')"),
 'completion flag not pulled before checks' => !str_contains($read('core/vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php'), "session()->pull('installer_admin_created')"),
 'completion flag consumed only after marker' => str_contains($read('core/vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php'), "session()->forget(['installer_schema_imported', 'installer_admin_created'])"),
 'install marker checked after write' => str_contains($read('core/vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php'), "is_file(storage_path('installed'))"),
 'no legacy env rewrite string' => !str_contains($read('core/vendor/rachidlaasri/laravel-installer/src/Helpers/EnvironmentManager.php'), '$envFileData ='),
];
foreach($checks as $label=>$pass) { if (!$pass) {fwrite(STDERR, "FAIL: $label\n");exit(1);} }
echo 'PASS36 contract: '.count($checks).'/'.count($checks)." checks passed\n";
