<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:staff:1']);$p->active=true;reject55(fn()=>$m->releaseAll());$p->active=false;$m->releaseAll();a55($m->held()===[]&&count($p->queries)===2,'deferred release failed');
echo 'pass55 04_release_after_transaction OK\n';
