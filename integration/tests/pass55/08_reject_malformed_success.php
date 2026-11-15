<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$s=new Pass55Stmt;$s->scalar='1anything';$p->queue=[$s];$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1']),'unavailable');a55(!$m->held(),'malformed success accepted');
echo 'pass55 08_reject_malformed_success OK\n';
