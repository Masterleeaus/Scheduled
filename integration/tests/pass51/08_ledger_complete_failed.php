<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRequestLedger(db51([],false)))->complete(key51(),7),'completion failed');
echo 'PASS51 08_ledger_complete_failed OK';
