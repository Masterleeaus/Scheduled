<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([row50()]),27,false);ok50($r['id']===27,'Mutation identity');
echo "PASS50 02_locked_booking_no_customer OK\n";
