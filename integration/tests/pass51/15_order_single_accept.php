<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
(new \Titan\BookingSidecar\BookingRescheduleOrderReader(db51([7])))->assertSingleBooking(11,7);
echo 'PASS51 15_order_single_accept OK';
