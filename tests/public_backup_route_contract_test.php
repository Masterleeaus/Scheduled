<?php
declare(strict_types=1);

/**
 * Static regression for Scheduled issue #76.
 *
 * Does not replace a disposable HTTP/Laravel route test: it prevents the
 * accidental reintroduction of an unauthenticated frontend backup route.
 * CI discovers every tests/*.php file, including this one.
 */
$path = dirname(__DIR__) . '/core/routes/web.php';
$routes = file_get_contents($path);
if ($routes === false) {
    fwrite(STDERR, "Could not inspect Scheduled web routes\n");
    exit(1);
}

$public = "Route::get('/backup', 'Front\\FrontendController@backup');";
$admin = "Route::get('/backup', 'Admin\\BackupController@index')->name('admin.backup.index');";
$guard = "Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'checkstatus', 'setLfmPath']], function () {";

$failures = [];
if (str_contains($routes, $public)) {
    $failures[] = 'obsolete public FrontendController backup route still registered';
}
if (substr_count($routes, "Route::get('/backup',") !== 1) {
    $failures[] = 'expected exactly one named GET backup route (authenticated admin only)';
}
if (!str_contains($routes, $admin)) {
    $failures[] = 'authenticated named admin backup route missing';
}
$guardPos = strpos($routes, $guard);
$adminPos = strpos($routes, $admin);
if ($guardPos === false || $adminPos === false || $adminPos < $guardPos) {
    $failures[] = 'admin backup route not placed after the authenticated admin group declaration';
}
if (count($failures) > 0) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL: $failure\n");
    }
    exit(1);
}

echo "PASS: only the authenticated admin backup route is registered\n";
