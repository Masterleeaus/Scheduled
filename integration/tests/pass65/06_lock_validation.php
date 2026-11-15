<?php
declare(strict_types=1);require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P65PDO();$m=new BookingLockManager($p);
reject65(fn()=>$m->acquire(['tb:staff:1','tb:invalid:2']), 'Invalid lock key');
reject65(fn()=>$m->acquire([]), 'cannot be empty');
reject65(fn()=>$m->acquire(['tb:staff:1'],31), 'timeout');
p65($p->sql===[],'validation issued unsafe SQL');
echo "pass65 06 validation OK\n";
