<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=['order_id'=>11,'order_duration'=>60];rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleOrderReader(db51([$r,$r])))->locked(11),'Ambiguous');
echo 'PASS51 11_order_duplicate OK';
