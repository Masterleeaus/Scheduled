<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([row50()]),2147483648,true),'Invalid booking ID');
echo "PASS50 10_locked_booking_overflow OK\n";
