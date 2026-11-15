<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P70PDO();$m=new BookingLockManager($p);$m->acquire(['tb:staff:1'],0);p70($p->statements[0]->params[1]===0.0,'nonblocking acquire changed');$m->releaseAll();
echo 'PASS70 06_zero_timeout.php OK'.PHP_EOL;
