<?php
declare(strict_types=1);
require dirname(__DIR__).'/pass20_support.php';
use Titan\BookingIntegration\{LifecyclePersistedTimestamp,LifecycleLegacyId,LifecycleReadbackRows,LifecycleFullBookingRow,LifecycleClientOrderRow,ConcurrencyFixtureIdentity};
function p59_booking():array{return ['id'=>'9','order_id'=>'77','client_id'=>'8','service_id'=>'1','method_id'=>'1','method_unit_id'=>'1','method_unit_qty'=>'1','method_unit_qty_rate'=>'65.00','staff_ids'=>'1','booking_date_time'=>'2027-01-10 09:00:00','booking_status'=>'A'];}
function p59_order():array{return ['id'=>'8','order_id'=>'77','order_duration'=>'60','client_name'=>'QA','client_email'=>'qa@example.invalid','client_phone'=>'0123456789'];}
foreach(['LifecyclePersistedTimestamp','LifecycleLegacyId','LifecycleReadbackRows','LifecycleFullBookingRow','LifecycleClientOrderRow','ConcurrencyFixtureIdentity'] as $class)class_alias('Titan\\BookingIntegration\\'.$class,$class);
