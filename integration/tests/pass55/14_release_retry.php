<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$ok=new Pass55Stmt;$bad=new Pass55Stmt;$bad->scalar=0;$good=new Pass55Stmt;$p->queue=[$ok,$bad,$good];$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:staff:1']);reject55(fn()=>$m->releaseAll());$m->releaseAll();a55(!$m->held()&&count($p->queries)===3,'release retry failed');
echo 'pass55 14_release_retry OK\n';
