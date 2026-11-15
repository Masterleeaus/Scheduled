<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P70PDO();$m=new BookingLockManager($p);reject70(fn()=>$m->acquire(['tb:staff:1'],5,-1),'Invalid absolute lock deadline');p70(!$p->statements,'query ran with invalid deadline');
echo 'PASS70 05_bad_deadline.php OK'.PHP_EOL;
