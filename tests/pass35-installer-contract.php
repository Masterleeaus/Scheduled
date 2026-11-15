<?php
$root = rtrim($argv[1] ?? dirname(__DIR__), '/');
$read = static fn($p) => file_get_contents($root.'/'.$p) ?: '';
$boot=$read('core/bootstrap/app.php');
$manager=$read('core/vendor/rachidlaasri/laravel-installer/src/Helpers/EnvironmentManager.php');
$final=$read('core/vendor/rachidlaasri/laravel-installer/src/Controllers/FinalController.php');
$assertions = [
    'bootstrap key executes before app' => strpos($boot,'InstallerBootKey::ensure') !== false && strpos($boot,'InstallerBootKey::ensure') < strpos($boot,'new Illuminate\\Foundation\\Application'),
    'wizard retains key' => str_contains($manager, "'APP_KEY='." . '$appKey'),
    'wizard validates runtime key' => str_contains($manager, "config('app.key') !== " . '$appKey'),
    'no key generation during wizard' => !str_contains($manager,'Str::random(32)'),
    'finish validates key' => str_contains($final,'base64_decode(substr($key, 7)'),
    'finish validates admin' => str_contains($final,"count() === 1"),
];
foreach($assertions as $name=>$ok) {echo ($ok?'PASS':'FAIL').": $name\n";if(!$ok)exit(1);}
