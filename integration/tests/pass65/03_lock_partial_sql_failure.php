<?php
declare(strict_types=1);require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P65PDO();$bad=new P65Statement();$bad->executeOk=false;$p->queue=[new P65Statement(),$bad];$m=new BookingLockManager($p);
reject65(fn()=>$m->acquire(['tb:staff:1','tb:staff:2']), 'query failed');
p65($m->held()===[],'failed SQL retained advisory lock');
p65(count(array_filter($p->sql,fn($q)=>str_contains($q,'RELEASE_LOCK')) )===1,'expected release after SQL error');
echo "pass65 03 partial failure cleanup OK\n";
