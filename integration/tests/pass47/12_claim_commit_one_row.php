<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->rows=1;$p->queue=[$s];p47((new \App\Booking\Public\Redemption\RedeemLedger($p))->commit(str_repeat('a',64),1,str_repeat('b',64),27)===true,'commit missing');
echo 'PASS47 12 claim_commit_one_row OK'.PHP_EOL;
