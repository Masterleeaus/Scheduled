<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P70PDO();$m=new BookingLockManager($p);$m->acquire(['tb:booking:1'],5);$m->acquire(['tb:booking:1','tb:staff:1'],5);p70(count($m->held())===2,'held lock lost or duplicated');$m->releaseAll();
echo 'PASS70 08_existing_held_skipped.php OK'.PHP_EOL;
