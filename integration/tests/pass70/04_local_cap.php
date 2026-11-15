<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P70PDO();$m=new BookingLockManager($p);$m->acquire(['tb:staff:1'],1,hrtime(true)+30000000000);p70($p->statements[0]->params[1]<=1,'external deadline extended configured timeout');$m->releaseAll();
echo 'PASS70 04_local_cap.php OK'.PHP_EOL;
