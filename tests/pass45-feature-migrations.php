<?php
// Standalone, no mysql driver needed: test feature migration discovery against actual files.
require __DIR__.'/../core/app/Support/Install/InitialFeatureMigrations.php';
use App\Support\Install\InitialFeatureMigrations;
$dir=__DIR__.'/../core/database/migrations';
$files=InitialFeatureMigrations::files($dir);
if (count($files)!==22) { fwrite(STDERR, "Expected 22 current feature migrations, got ".count($files)."\n"); exit(1); }
foreach ($files as $basename=>$path) {
    if (!str_starts_with($basename,'2026_') || !is_file($path)) exit(2);
}
$expected=['2026_11_10_000003_create_titan_site_templates', '2026_11_11_140001_create_titan_business_tenants','2026_11_11_270001_create_titan_site_booking_service_maps','2026_11_15_000001_create_central_processed_events','2026_11_15_000002_create_booking_event_outbox_tables'];
foreach($expected as $name){if(!isset($files[$name]))exit(3);}
$tmp=sys_get_temp_dir().'/pass45-'.bin2hex(random_bytes(4));
mkdir($tmp);
try {try { InitialFeatureMigrations::files($tmp); exit(4); } catch(RuntimeException $e) {} }
finally {rmdir($tmp);}
echo "PASS: 22 feature migrations identified; invalid path rejected\n";
