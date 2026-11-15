<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRequestLedger(db51([],true,1,false)))->getForUpdate(key51()),'transaction');
echo 'PASS51 05_ledger_transaction OK';
