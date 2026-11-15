<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([]),27,true),'not found');
echo "PASS50 03_locked_booking_missing OK\n";
