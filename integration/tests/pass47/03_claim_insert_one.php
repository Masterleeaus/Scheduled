<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->rows=1;$p->queue=[$s];p47((new \App\Booking\Public\Redemption\RedeemLedger($p))->claim(str_repeat('a',64),1,str_repeat('b',64))===true,'claim missing');
echo 'PASS47 03 claim_insert_one OK'.PHP_EOL;
