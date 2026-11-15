<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$d=db51();$d->failPrepare=true;rejects51(fn()=>(new \Titan\BookingSidecar\BookingRequestLedger($d))->getForUpdate(key51()),'lookup failed');
echo 'PASS51 03_ledger_lookup_prepare OK';
