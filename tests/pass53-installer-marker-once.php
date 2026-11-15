<?php
// Executable standalone regression for exactly-once installation finalisation.
$root = sys_get_temp_dir().'/plusagency-marker-'.bin2hex(random_bytes(6));
if (!mkdir($root, 0700)) { throw new RuntimeException('Cannot create test directory'); }
function storage_path($name = '') { global $root; return $root.'/'.ltrim($name, '/'); }
function trans($key) { return 'Installation completed at '; }
require __DIR__.'/pass53-marker-fixture/InstalledFileManager.php';
$manager = new \RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager();
$passes = 0;
function ok($value, $label) { global $passes; if (!$value) { throw new RuntimeException($label); } $passes++; }
try {
  ok(!file_exists(storage_path('installed')), 'Clean start');
  $created = $manager->create();
  ok(str_starts_with($created, 'Installation completed at '), 'New marker content');
  ok(file_exists(storage_path('installed')), 'Marker exists');
  ok(file_get_contents(storage_path('installed')) === $created, 'Marker persisted');
  $rejected = false;
  try { $manager->update(); } catch (RuntimeException $e) { $rejected = true; }
  ok($rejected, 'Repeated finalisation rejected');
  ok(file_get_contents(storage_path('installed')) === $created, 'Repeated attempt cannot alter marker');
  unlink(storage_path('installed'));
  file_put_contents(storage_path('installed'), "existing installation\n");
  $rejected = false;
  try { $manager->create(); } catch (RuntimeException $e) { $rejected = true; }
  ok($rejected, 'Existing install rejected');
  ok(file_get_contents(storage_path('installed')) === "existing installation\n", 'Existing marker preserved');
  unlink(storage_path('installed'));
  mkdir(storage_path('installed'));
  $rejected = false;
  try { $manager->create(); } catch (RuntimeException $e) { $rejected = true; }
  ok($rejected, 'Directory collision rejected');
  ok(is_dir(storage_path('installed')), 'Collision path preserved');
  rmdir(storage_path('installed'));
  echo "PASS: {$passes}/{$passes} marker assertions\n";
} finally { @unlink(storage_path('installed')); @rmdir($root); }
