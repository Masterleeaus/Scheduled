<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->ok=false;$p->queue=[$s];fails47(fn()=>(new \App\Booking\Public\Redemption\RedeemLedger($p))->read(str_repeat('a',64)),'read failed');
echo 'PASS47 06 claim_read_execute_false OK'.PHP_EOL;
