<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$s=new Pass55Stmt;$s->scalar=false;$p->queue=[$s];$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1']),'unavailable');a55(!$m->held(),'false lock retained');
echo 'pass55 07_false_lock_result OK\n';
