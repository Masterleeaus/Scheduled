<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>(new \Titan\BookingSidecar\BookingLegacyBookingReader(reader50([])))->getForUpdate(27),'not found');
echo "PASS50 16_legacy_reader_missing OK\n";
