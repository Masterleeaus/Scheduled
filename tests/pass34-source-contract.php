<?php
$root = dirname(__DIR__);
$routes = file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Routes/web.php');
$final = file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php');
$admin = file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Controllers/AdministratorController.php');
$env = file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php');
$config = file_get_contents($root.'/core/config/installer.php');
$checks = [
    'admin GET route' => str_contains($routes,"Route::get('administrator'"),
    'admin POST route' => str_contains($routes,"Route::post('administrator'"),
    'legacy classic route removed' => !str_contains($routes,"Route::post('environment/saveClassic'"),
    'legacy database bypass removed' => !str_contains($routes,"Route::get('database'"),
    'admin gate' => str_contains($admin,"installer_schema_imported"),
    'admin hash' => str_contains($admin,'Hash::make'),
    'admin existing account guard' => str_contains($admin,"table('admins')->exists"),
    'finish session gate' => str_contains($final,"pull('installer_admin_created')"),
    'finish DB guard' => str_contains($final,"table('admins')->where('status', 1)->exists"),
    'import redirect' => str_contains($env,"route('LaravelInstaller::administrator')"),
    'key not rotated' => str_contains($config,"'key' => false"),
    'updater disabled' => str_contains($config,"'updaterEnabled' => false"),
];
foreach ($checks as $name=>$ok) {if (!$ok){fwrite(STDERR,"FAIL: $name\n");exit(1);}}
echo 'PASS: '.count($checks)." secure-install contract checks\n";
