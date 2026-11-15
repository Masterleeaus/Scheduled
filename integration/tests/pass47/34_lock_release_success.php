<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;$p->active=false;
$s=new Pass47Statement;$s->column=1;$u=new Pass47Statement;$u->column=1;$p->queue=[$s,$u];$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:abc123abc123:staff:1']);$m->releaseAll();p47($m->held()===[],'lock still held');
echo 'PASS47 34 lock_release_success OK'.PHP_EOL;
