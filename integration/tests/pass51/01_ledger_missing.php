<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$v=(new \Titan\BookingSidecar\BookingRequestLedger(db51()))->getForUpdate(key51());p51($v===null,'Missing key');
echo 'PASS51 01_ledger_missing OK';
