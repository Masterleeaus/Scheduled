<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject55(fn()=>\Titan\BookingSidecar\BookingLockKeys::forStaff([2147483648]),'Invalid staff');
echo 'pass55 17_reject_oversized_staff OK\n';
