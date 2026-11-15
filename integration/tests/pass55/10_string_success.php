<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$s=new Pass55Stmt;$s->scalar='1';$u=new Pass55Stmt;$u->scalar='1';$p->queue=[$s,$u];$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:staff:2']);$m->releaseAll();a55(!$m->held(),'valid string success rejected');
echo 'pass55 10_string_success OK\n';
