<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRequestLedger(db51([],false)))->claim(key51(),hash51()),'claim failed');
echo 'PASS51 07_ledger_claim_failed OK';
