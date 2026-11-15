<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$p->active=true;$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1']),'before a transaction');a55(!$p->queries,'SQL in transaction');
echo 'pass55 02_reject_transaction_acquire OK\n';
