<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([row50()]),27,true);ok50($r['client_id']===32,'Customer identity');
echo "PASS50 01_locked_booking_success OK\n";
