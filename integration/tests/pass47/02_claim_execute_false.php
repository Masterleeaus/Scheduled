<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->ok=false;$p->queue=[$s];fails47(fn()=>(new \App\Booking\Public\Redemption\RedeemLedger($p))->claim(str_repeat('a',64),1,str_repeat('b',64)),'insert failed');
echo 'PASS47 02 claim_execute_false OK'.PHP_EOL;
