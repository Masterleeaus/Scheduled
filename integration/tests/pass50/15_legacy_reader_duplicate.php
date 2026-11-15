<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>(new \Titan\BookingSidecar\BookingLegacyBookingReader(reader50([row50(),row50()])))->getForUpdate(27),'Duplicate');
echo "PASS50 15_legacy_reader_duplicate OK\n";
