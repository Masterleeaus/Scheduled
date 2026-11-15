<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$ok=new Pass55Stmt;$bad=new Pass55Stmt;$bad->scalar=0;$release=new Pass55Stmt;$release->scalar=0;$p->queue=[$ok,$bad,$release];$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1','tb:staff:2']),'cleanup failed');a55($m->held()===['tb:staff:1'],'uncertain lease lost');
echo 'pass55 16_failed_acquire_cleanup_uncertain OK\n';
