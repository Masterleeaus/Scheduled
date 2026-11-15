<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
a55(\Titan\BookingSidecar\BookingLockKeys::forBooking(2147483647)==='tb:booking:2147483647','max booking id rejected');
echo 'pass55 20_accept_max_booking OK\n';
