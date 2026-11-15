<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->failPrepare=true;fails47(fn()=>(new \App\Booking\Public\Redemption\RedeemLedger($p))->claim(str_repeat('a',64),1,str_repeat('b',64)),'insert failed');
echo 'PASS47 01 claim_prepare_rejected OK'.PHP_EOL;
