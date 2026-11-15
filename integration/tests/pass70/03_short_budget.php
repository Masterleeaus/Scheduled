<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P70PDO();$m=new BookingLockManager($p);$deadline=hrtime(true)+300000000;$m->acquire(['tb:staff:1'],5,$deadline);p70($p->statements[0]->params[1]<=0.3,'deadline failed to cap timeout');$m->releaseAll();
echo 'PASS70 03_short_budget.php OK'.PHP_EOL;
