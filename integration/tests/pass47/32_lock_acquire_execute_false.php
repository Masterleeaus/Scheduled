<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;$p->active=false;
$s=new Pass47Statement;$s->ok=false;$p->queue=[$s];fails47(fn()=>(new \Titan\BookingSidecar\BookingLockManager($p))->acquire(['tb:abc123abc123:staff:1']),'Booking lock query failed');
echo 'PASS47 32 lock_acquire_execute_false OK'.PHP_EOL;
