<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$ok=new Pass55Stmt;$bad=new Pass55Stmt;$bad->scalar=0;$p->queue=[$ok,$bad];$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:staff:1']);reject55(fn()=>$m->releaseAll(),'could not be released');a55(count($m->held())===1,'uncertain lock forgotten');
echo 'pass55 13_failed_release_retained OK\n';
