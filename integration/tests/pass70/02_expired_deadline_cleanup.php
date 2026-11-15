<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P70PDO();$m=new BookingLockManager($p);$m->acquire(['tb:booking:1'],5);reject70(fn()=>$m->acquire(['tb:staff:1'],5,hrtime(true)-1000000),'deadline exceeded');p70($m->held()===[],'held lock not released on failed phase');
echo 'PASS70 02_expired_deadline_cleanup.php OK'.PHP_EOL;
