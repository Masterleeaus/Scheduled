<?php
declare(strict_types=1);require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P65PDO();$blocked=new P65Statement();$blocked->scalar=0;
$p->queue=[new P65Statement(),$blocked];$m=new BookingLockManager($p);
reject65(fn()=>$m->acquire(['tb:staff:1','tb:staff:2']), 'unavailable');
p65($m->held()===[],'lock leaked after contention');
p65(in_array('SELECT RELEASE_LOCK(?)',$p->sql,true),'acquired lock not released');
echo "pass65 02 contention cleanup OK\n";
