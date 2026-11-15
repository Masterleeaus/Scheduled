<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->rows=0;$p->queue=[$s];p47((new \App\Booking\Public\Redemption\RedeemLedger($p))->commit(str_repeat('a',64),1,str_repeat('b',64),27)===false,'commit should fail');
echo 'PASS47 11 claim_commit_zero_rows OK'.PHP_EOL;
