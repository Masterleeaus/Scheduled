<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=(new \Titan\BookingSidecar\BookingLegacyBookingReader(reader50([row50()])))->getForUpdate(27);ok50($r['client_id']===32,'Legacy reader owns row');
echo "PASS50 14_legacy_reader_success OK\n";
