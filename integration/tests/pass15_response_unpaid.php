<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\BookingWriteResponseGuard;
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
$r=BookingWriteResponseGuard::sanitize(['booking_id'=>10,'order_id'=>11,'replayed'=>false,'payment_status'=>'paid']);
check($r['payment_status']==='unpaid' && $r['requires_payment_processing']===true,'new booking wrongly settled');
$replay=BookingWriteResponseGuard::sanitize(['booking_id'=>10,'order_id'=>11,'replayed'=>true,'payment_status'=>'unpaid']);
check($replay['payment_status']==='unknown' && $replay['requires_payment_processing']===false,'existing booking could trigger another payment');
echo 'pass15 response_unpaid ok'.PHP_EOL;
