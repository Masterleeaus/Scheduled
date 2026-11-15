<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$a=['id'=>7,'order_id'=>20,'client_id'=>9,'service_id'=>10,'method_id'=>3,'method_unit_id'=>2,'method_unit_qty'=>1,'rate'=>1250,'booking_date_time'=>'2026-12-01 10:00:00','staff_ids'=>[4],'booking_status'=>'A'];$b=$a;$b['rate']=2500;$thrown=false;try{\Titan\BookingIntegration\LifecycleImmutableIdentity::verify($a,$b);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('economic mutation accepted');
echo "PASS pass27_identity_price_changed\n";
