<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$r=row50();$r['id']=28;fail50(fn()=>\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([$r]),27,true),'Wrong locked booking');
echo "PASS50 05_locked_booking_wrong_id OK\n";
