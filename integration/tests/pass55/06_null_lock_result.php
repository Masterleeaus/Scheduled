<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$s=new Pass55Stmt;$s->scalar=null;$p->queue=[$s];$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1']),'unavailable');a55($m->held()===[],'invalid lock result retained');
echo 'pass55 06_null_lock_result OK\n';
