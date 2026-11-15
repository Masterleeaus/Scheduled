<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject55(fn()=>\Titan\BookingSidecar\BookingLockKeys::forBooking(2147483648),'legacy range');
echo 'pass55 19_reject_oversized_booking OK\n';
