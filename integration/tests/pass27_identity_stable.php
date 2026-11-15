<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$a=['id'=>7,'order_id'=>20,'client_id'=>9,'service_id'=>10,'method_id'=>3,'method_unit_id'=>2,'method_unit_qty'=>1,'rate'=>1250,'booking_date_time'=>'2026-12-01 10:00:00','staff_ids'=>[4],'booking_status'=>'A'];\Titan\BookingIntegration\LifecycleImmutableIdentity::verify($a,$a);
echo "PASS pass27_identity_stable\n";
