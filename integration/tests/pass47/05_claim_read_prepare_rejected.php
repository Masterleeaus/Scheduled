<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->failPrepare=true;fails47(fn()=>(new \App\Booking\Public\Redemption\RedeemLedger($p))->read(str_repeat('a',64)),'read failed');
echo 'PASS47 05 claim_read_prepare_rejected OK'.PHP_EOL;
