<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$a=['order_id'=>20,'order_duration'=>120];$b=$a;$b['order_duration']=130;$thrown=false;try{\Titan\BookingIntegration\LifecycleOrderIntegrity::verify($a,$b);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('duration mutation accepted');
echo "PASS pass27_order_duration_changed\n";
