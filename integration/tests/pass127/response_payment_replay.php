<?php
declare(strict_types=1);
require __DIR__.'/../../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\BookingPaymentBoundary;
$checks=0;
function ensure(bool $condition,string $name):void {global $checks; $checks++; if(!$condition)throw new \RuntimeException($name);}
$new=BookingPaymentBoundary::forBooking(['booking_id'=>12,'order_id'=>31,'replayed'=>false,'status'=>'active','payment_status'=>'paid','requires_payment_processing'=>false]);
ensure($new['payment_status']==='unpaid','new booking is not settled by creation');
ensure($new['requires_payment_processing']===true,'new booking payment path permitted');
ensure($new['payment_reconciliation_required']===false,'new booking has no prior payment to reconcile');
ensure($new['order_id']===31,'new booking order preserved');
$replay=BookingPaymentBoundary::forBooking(['booking_id'=>12,'order_id'=>31,'replayed'=>true,'status'=>'active','payment_status'=>'unpaid','requires_payment_processing'=>true]);
ensure($replay['payment_status']==='unknown','replay must not assert unpaid');
ensure($replay['requires_payment_processing']===false,'replay must not trigger payment');
ensure($replay['payment_reconciliation_required']===true,'replay requires payment reconciliation');
ensure($replay['booking_id']===12&&$replay['order_id']===31,'replay evidence IDs preserved');
foreach([
 ['booking_id'=>0,'order_id'=>31,'replayed'=>false],
 ['booking_id'=>'12','order_id'=>31,'replayed'=>false],
 ['booking_id'=>12,'order_id'=>0,'replayed'=>false],
 ['booking_id'=>12,'order_id'=>'31','replayed'=>false],
 ['booking_id'=>12,'order_id'=>31],
 ['booking_id'=>12,'order_id'=>31,'replayed'=>'false'],
 ['booking_id'=>2147483648,'order_id'=>31,'replayed'=>false],
 ['booking_id'=>12,'order_id'=>2147483648,'replayed'=>false],
] as $case){$thrown=false;try{BookingPaymentBoundary::forBooking($case);}catch(\DomainException $e){$thrown=true;}ensure($thrown,'reject malformed booking response');}
echo "PASS pass127 response payment replay {$checks} assertions\n";
