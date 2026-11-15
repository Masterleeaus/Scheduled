<?php
$root = dirname(__DIR__);
$base = $root.'/core/vendor/rachidlaasri/laravel-installer/src/';
$routes = file_get_contents($base.'Routes/web.php');
$controller = file_get_contents($base.'Controllers/FinalController.php');
$review = file_get_contents($root.'/core/resources/views/vendor/installer/final-review.blade.php');
$finished = file_get_contents($root.'/core/resources/views/vendor/installer/finished.blade.php');
$checks = [
  'GET final is review only' => (bool) preg_match("~Route::get\('final',\s*\[\s*'as' => 'final',\s*'uses' => 'FinalController@review'~", $routes),
  'POST completes install' => (bool) preg_match("~Route::post\('final/complete',\s*\[\s*'as' => 'finalComplete',\s*'uses' => 'FinalController@finish'~", $routes),
  'review renders without marker changes' => str_contains($controller, "return view('vendor.installer.final-review')"),
  'POST view carries CSRF' => str_contains($review,'@csrf'),
  'POST target matches route' => str_contains($review,"route('LaravelInstaller::finalComplete')"),
  'review uses POST form' => str_contains($review,'method="post"'),
  'completion checks markers and admin' => str_contains($controller,"$".'this->requireVerifiedSetup($request)') && str_contains($controller,"'administrator_count'"),
  'completion verifies storage marker' => str_contains($controller,"is_file(storage_path('installed'))"),
  'final view never advertises default account' => str_contains($finished,'No default login'),
  'final view correct preflight path' => str_contains($finished,'tools/install-preflight.php'),
  'completion preserves session on failure' => str_contains($controller,"$".'request->session()->forget(') && strpos($controller, 'session()->forget') > strpos($controller, '$fileManager->update()'),
  'review requires session provenance' => str_contains($controller,"'installer_schema_imported'") && str_contains($controller,"'installer_admin_created'"),
];
foreach ($checks as $name => $pass) { echo ($pass?'PASS ':'FAIL ').$name.PHP_EOL; }
if (in_array(false,$checks,true)) exit(1);
echo count($checks).'/'.count($checks).' checks passed'.PHP_EOL;
