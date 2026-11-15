<?php
// Pass40: branch reconciliation and dependency safeguards.
$root=dirname(__DIR__);
$installer=file_get_contents($root.'/core/vendor/rachidlaasri/laravel-installer/src/Controllers/EnvironmentController.php');
$native=file_get_contents($root.'/core/app/Booking/NativeCleantoCatalogue.php');
$gateway=file_get_contents($root.'/core/app/Booking/BookingCatalogueGateway.php');
$config=file_get_contents($root.'/core/config/booking_internal.php');
$checks=[
 'env-before-import' => strpos($installer,'saveFileWizard($request)') < strpos($installer,'LegacySqlImporter::import('),
 'db-empty-before-env' => strpos($installer,'LegacySqlImporter::ensureEmptyDatabase(') < strpos($installer,'saveFileWizard($request)'),
 'session-marker-after-import' => strpos($installer,"session()->put('installer_schema_imported'") > strpos($installer,'LegacySqlImporter::import('),
 'native-disabled-default' => str_contains($config,"'native_catalogue_tenants' => []"),
 'native-registry-db-binding' => str_contains($native,"tenant['database_reference']"),
 'native-live-db-identity' => str_contains($native,'SELECT DATABASE()'),
 'native-mysql-only' => str_contains($native,"!== 'mysql'"),
 'native-no-data-writes' => !preg_match('/\b(?:INSERT|UPDATE|DELETE|TRUNCATE|REPLACE)\s+(?:INTO|FROM|TABLE|ct_)/i',$native),
 'gateway-still-defaults-to-sidecar' => str_contains($gateway,"'booking_catalogue'"),
 'native-failure-not-fallback' => strpos($gateway,'$this->native->read(') < strpos($gateway,'}else{'),
 'booking-write-disabled' => str_contains($gateway,"'booking_writes_enabled'=>false"),
];
foreach($checks as $name=>$ok){echo ($ok?'PASS':'FAIL')." $name\n";if(!$ok)exit(1);}echo count($checks)."/".count($checks)." passed\n";
