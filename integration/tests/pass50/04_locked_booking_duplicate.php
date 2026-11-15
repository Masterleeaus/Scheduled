<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([row50(),row50()]),27,true),'Duplicate');
echo "PASS50 04_locked_booking_duplicate OK\n";
