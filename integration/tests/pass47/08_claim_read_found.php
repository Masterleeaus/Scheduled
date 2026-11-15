<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->data=[['state'=>'claimed','owner_id'=>1]];$p->queue=[$s];p47((new \App\Booking\Public\Redemption\RedeemLedger($p))->read(str_repeat('a',64))['state']==='claimed','read claim');
echo 'PASS47 08 claim_read_found OK'.PHP_EOL;
