<?php
declare(strict_types=1);
require_once __DIR__.'/../../app/Booking/Diagnostics/BookingSchemaSnapshot.php';
use App\Booking\Diagnostics\BookingSchemaSnapshot as Snapshot;
$required=['ct_bookings'=>['id','order_id'], 'ct_staff'=>['id']];
$actual=['ct_bookings'=>['id','order_id','status'],'ct_staff'=>['id']];
if(Snapshot::missing($required,$actual)!==[]) throw new RuntimeException('Valid fixture unexpectedly incomplete');
if(Snapshot::missing($required,['ct_bookings'=>['id']])!==['ct_bookings.order_id','ct_staff.id']) throw new RuntimeException('Missing schema diff incorrect');
try {Snapshot::missing(['x'=>[null]],[]);throw new RuntimeException('Unvalidated schema contract');}catch(InvalidArgumentException $e){}
$code=file_get_contents(__DIR__.'/../../app/Console/Commands/VerifyCleantoTenants.php');
if(strpos($code,'booking:verify-tenants')===false || strpos($code,'production_certified')===false)throw new RuntimeException('Artisan inspection contract missing');
echo "Pass 10 schema snapshot tests passed\n";
