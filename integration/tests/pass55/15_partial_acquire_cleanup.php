<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$ok=new Pass55Stmt;$bad=new Pass55Stmt;$bad->scalar=0;$release=new Pass55Stmt;$p->queue=[$ok,$bad,$release];$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1','tb:staff:2']),'unavailable');a55(!$m->held()&&count($p->queries)===3,'partial acquire cleanup failed');
echo 'pass55 15_partial_acquire_cleanup OK\n';
