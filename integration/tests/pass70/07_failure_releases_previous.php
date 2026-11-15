<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P70PDO();$f=new P70Statement();$f->scalar=0;$p->queue=[new P70Statement(),$f];$m=new BookingLockManager($p);reject70(fn()=>$m->acquire(['tb:staff:1','tb:staff:2'],5),'Booking lock unavailable');p70($m->held()===[],'partial acquired locks not released');
echo 'PASS70 07_failure_releases_previous.php OK'.PHP_EOL;
