<?php
declare(strict_types=1);
// Standalone contract test: no database, HTTP, or Laravel boot required.
$root = dirname(__DIR__);
$reader = file_get_contents($root.'/app/Booking/NativeCleantoCatalogue.php');
$gateway = file_get_contents($root.'/app/Booking/BookingCatalogueGateway.php');
$config = file_get_contents($root.'/config/booking_internal.php');
$assertions = [
 'native reads original ct_services' => str_contains($reader, 'FROM ct_services WHERE'),
 'native reads original ct_services_method' => str_contains($reader, 'FROM ct_services_method'),
 'native reads original ct_service_methods_units' => str_contains($reader, 'FROM ct_service_methods_units'),
 'native reads original ct_users' => str_contains($reader, 'FROM ct_users WHERE'),
 'owner opt in' => str_contains($gateway, 'native_catalogue_tenants'),
 'fallback remains sidecar' => str_contains($gateway, "'booking_catalogue'"),
 'registered tenant reference required' => str_contains($reader, "tenant['database_reference']"),
 'connected database checked' => str_contains($reader, 'SELECT DATABASE()'),
 'only mysql driver' => str_contains($reader, "!== 'mysql'"),
 'limit services' => str_contains($reader, ', 250)'),
 'limit methods' => str_contains($reader, ', 1000)'),
 'limit units' => str_contains($reader, ', 3000)'),
 'limit staff' => str_contains($reader, ', 250)'),
 'validated existing catalogue parser' => str_contains($reader, 'CatalogSnapshot::build'),
 'no write query' => !preg_match('/\b(INSERT|UPDATE|DELETE|TRUNCATE)\b/i', $reader),
 'opt-in disabled by default' => str_contains($config, "'native_catalogue_tenants' => []"),
 'writes remain off' => str_contains($gateway, "'booking_writes_enabled'=>false"),
];
foreach ($assertions as $label => $pass) { echo ($pass?'PASS':'FAIL')." $label\n"; if (!$pass) exit(1); }
echo 'TOTAL '.count($assertions).' assertions passed'.PHP_EOL;
