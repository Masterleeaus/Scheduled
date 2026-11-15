<?php
require __DIR__.'/../core/app/Support/Install/InstallerLicenseVerifier.php';
use App\Support\Install\InstallerLicenseVerifier as V;
$checks = [
    V::matches(['buyer'=>'owner','item'=>['id'=>24646161]], 'owner', 24646161),
    V::matches(['buyer'=>'owner','item'=>['id'=>'24646161']], 'owner', 24646161),
    !V::matches([], 'owner', 24646161),
    !V::matches(['buyer'=>'wrong','item'=>['id'=>24646161]], 'owner', 24646161),
    !V::matches(['buyer'=>'owner','item'=>['id'=>0]], 'owner', 24646161),
    !V::matches(['buyer'=>'owner','item'=>['id'=>24646161.5]], 'owner', 24646161),
    !V::matches(['buyer'=>['owner'],'item'=>['id'=>24646161]], 'owner', 24646161),
];
$root=dirname(__DIR__);
$license=file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Controllers/LicenseController.php');
$env=file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php');
$conf=file_get_contents($root.'/core/config/installer.php');
$checks[] = strpos($license, 'Bearer d3eCIK') === false;
$checks[] = strpos($license, 'dd(') === false && strpos($license, 'echo ') === false;
$checks[] = strpos($license, 'installer_license_verified') !== false;
$checks[] = substr_count($env, "session('installer_license_verified')") === 2;
$checks[] = strpos($conf, 'INSTALLER_ENVATO_TOKEN') !== false;
$checks[] = strpos($license, 'https://api.envato.com/') !== false;
foreach ($checks as $i=>$ok) { if (!$ok) {fwrite(STDERR, "Failure ".($i+1)."\n");exit(1);} }
echo count($checks)."/".count($checks)." license security checks passed\n";
