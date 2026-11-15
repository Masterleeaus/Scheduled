<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:staff:1']);$p->active=true;reject55(fn()=>$m->releaseAll(),'after transaction');a55(count($m->held())===1&&count($p->queries)===1,'early release lost lease');
echo 'pass55 03_reject_transaction_release OK\n';
