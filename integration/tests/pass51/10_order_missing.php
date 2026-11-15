<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleOrderReader(db51()))->locked(11),'not found');
echo 'PASS51 10_order_missing OK';
