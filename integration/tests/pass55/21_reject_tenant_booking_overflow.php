<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
reject55(fn()=>\Titan\BookingSidecar\BookingLockKeys::forTenantBooking('abc123abc123',2147483648),'legacy range');
echo 'pass55 21_reject_tenant_booking_overflow OK\n';
