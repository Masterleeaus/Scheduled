<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRequestLedger(db51([],false)))->getForUpdate(key51()),'lookup failed');
echo 'PASS51 02_ledger_lookup_execute OK';
