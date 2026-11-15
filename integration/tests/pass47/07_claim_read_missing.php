<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$p->queue=[$s];p47((new \App\Booking\Public\Redemption\RedeemLedger($p))->read(str_repeat('a',64))===null,'missing claim');
echo 'PASS47 07 claim_read_missing OK'.PHP_EOL;
