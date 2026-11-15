<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$thrown=false;try{\Titan\BookingIntegration\LifecycleSiblingRow::parse(['id'=>2,'order_id'=>20,'booking_date_time'=>'bad','staff_ids'=>'4','booking_status'=>'A']);}catch(RuntimeException $e){$thrown=true;}if(!$thrown)throw new Exception('bad time accepted');
echo "PASS pass27_sibling_row_bad\n";
