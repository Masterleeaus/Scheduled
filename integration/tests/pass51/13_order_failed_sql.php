<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleOrderReader(db51([],false)))->locked(11),'lookup failed');
echo 'PASS51 13_order_failed_sql OK';
