<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleOrderReader(db51([7,8])))->assertSingleBooking(11,7),'Shared order');
echo 'PASS51 14_order_single_reject OK';
