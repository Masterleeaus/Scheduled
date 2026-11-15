<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([row50()],false),27,true),'query failed');
echo "PASS50 06_locked_booking_failed_execute OK\n";
