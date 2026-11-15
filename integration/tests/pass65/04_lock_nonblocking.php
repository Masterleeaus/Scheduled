<?php
declare(strict_types=1);require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\BookingLockManager;
$p=new P65PDO();$m=new BookingLockManager($p);$m->acquire(['tb:staff:1'],0);
p65($p->statements[0]->params[1]===0.0,'zero timeout must be nonblocking');$m->releaseAll();
echo "pass65 04 nonblocking OK\n";
