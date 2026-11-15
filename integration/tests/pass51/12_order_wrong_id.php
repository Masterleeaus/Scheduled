<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleOrderReader(db51([['order_id'=>12,'order_duration'=>60]])))->locked(11),'Wrong order');
echo 'PASS51 12_order_wrong_id OK';
