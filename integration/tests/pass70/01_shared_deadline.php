<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P70PDO();$m=new BookingLockManager($p);$deadline=hrtime(true)+1700000000;$m->acquire(['tb:booking:1'],5,$deadline);usleep(100000);$m->acquire(['tb:staff:2'],5,$deadline);p70($p->statements[1]->params[1]<1.65,'second acquisition incorrectly reset timeout');$m->releaseAll();
echo 'PASS70 01_shared_deadline.php OK'.PHP_EOL;
