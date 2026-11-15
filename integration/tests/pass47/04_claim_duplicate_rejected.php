<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->rows=0;$p->queue=[$s];p47((new \App\Booking\Public\Redemption\RedeemLedger($p))->claim(str_repeat('a',64),1,str_repeat('b',64))===false,'duplicate claim');
echo 'PASS47 04 claim_duplicate_rejected OK'.PHP_EOL;
