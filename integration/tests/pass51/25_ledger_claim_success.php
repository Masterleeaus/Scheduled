<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
(new \Titan\BookingSidecar\BookingRequestLedger(db51()))->claim(key51(),hash51());
echo 'PASS51 25_ledger_claim_success OK';
