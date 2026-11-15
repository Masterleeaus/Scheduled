<?php
declare(strict_types=1);
$root=dirname(__DIR__,2);
$read=function(string $path)use($root):string{$data=file_get_contents($root.'/'.$path);if($data===false)throw new RuntimeException('Missing source: '.$path);return $data;};
$command=$read('app/Console/Commands/VerifyCleantoTenants.php');
$probe=$read('app/Booking/Diagnostics/BookingTenantProbe.php');
$snapshot=$read('app/Booking/Diagnostics/BookingSchemaSnapshot.php');
$controller=$read('app/Http/Controllers/Front/BookingPublicSubmissionController.php');
$checks=[
 'operator-only Artisan command'=>str_contains($command,'booking:verify-tenants'),
 'missing PDO refuses inspection'=>str_contains($command,"extension_loaded('pdo_mysql')"),
 'no public release auto-toggle'=>!str_contains($command,'public_commit_enabled = true'),
 'read-only information schema'=>str_contains($snapshot,'information_schema.COLUMNS'),
 'prepared metadata query'=>str_contains($snapshot,'TABLE_SCHEMA = ?'),
 'connection tenant identity pinned'=>str_contains($probe,'hash_equals'),
 'no production acceptance fabrication'=>str_contains($probe,"'production_certified'] = false"),
 'live concurrency not assumed'=>str_contains($probe,"'live_concurrency_verified'] = false"),
 'financial settlement not assumed'=>str_contains($probe,"'payment_settlement_verified'] = false"),
 'real executor remains guarded'=>str_contains($controller,"public_commit_enabled', false") && str_contains($controller,'CheckoutSubmissionExecutor::class'),
];
foreach($checks as $label=>$ok){if(!$ok)throw new RuntimeException($label.' FAILED');echo 'PASS '.$label.PHP_EOL;}
