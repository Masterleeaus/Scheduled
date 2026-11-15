<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([row50()]),0,true),'Invalid booking ID');
echo "PASS50 09_locked_booking_zero_id OK\n";
