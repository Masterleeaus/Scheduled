<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRequestLedger(db51([],true,0)))->claim(key51(),hash51()),'exactly one row');
echo 'PASS51 06_ledger_claim_zero OK';
