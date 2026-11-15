<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRequestLedger(db51([['fingerprint'=>hash51(),'state'=>'completed','booking_id'=>7],['fingerprint'=>hash51(),'state'=>'completed','booking_id'=>8]])))->getForUpdate(key51()),'Ambiguous');
echo 'PASS51 04_ledger_duplicate OK';
