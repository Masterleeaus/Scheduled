<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail50(fn()=>\Titan\BookingSidecar\BookingLockedRowProof::fetch(reader50([row50()],true,false),27,true),'requires transaction');
echo "PASS50 08_locked_booking_no_transaction OK\n";
