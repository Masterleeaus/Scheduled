<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
(new \Titan\BookingSidecar\BookingRequestLedger(db51()))->complete(key51(),7);
echo 'PASS51 09_ledger_complete_ok OK';
