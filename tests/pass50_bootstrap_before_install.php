<?php
/** Regression contract: first-install boot cannot require a populated database. */
$root = dirname(__DIR__);
$provider = file_get_contents($root . '/core/app/Providers/AppServiceProvider.php');
$routes = file_get_contents($root . '/core/routes/web.php');
$kernel = file_get_contents($root . '/core/app/Http/Kernel.php');
$middlewarePath = $root . '/core/app/Http/Middleware/RedirectUninstalledToInstaller.php';
$middleware = file_get_contents($middlewarePath);
$checks = [
 'no provider hard-exit' => strpos($provider, 'header("Location: install/")') === false && !preg_match('/\bexit\s*;/', $provider),
 'installer redirect scoped to HTTP kernel' => strpos($kernel, 'RedirectUninstalledToInstaller::class') !== false,
 'redirect targets real installer route' => strpos($middleware, "redirect('/install')") !== false,
 'installer pages exempt from redirection' => strpos($middleware, '! $request->is(\'install/*\')') !== false,
 'installation marker checked by middleware' => strpos($middleware, "storage_path('installed')") !== false,
 'dynamic permalink queries gated on installed state' => strpos($routes, "if (is_file(storage_path('installed'))) {") !== false,
 'both dynamic permalink groups remain' => substr_count($routes, "Permalink::where('details',") === 2,
 'newer application routes retained' => strpos($routes, "front.sites.show") !== false && strpos($routes, 'front.landing.show') !== false,
];
foreach ($checks as $name => $passed) {
    printf("%s %s\n", $passed ? 'PASS' : 'FAIL', $name);
    if (!$passed) { exit(1); }
}
printf("PASS %d checks\n", count($checks));
