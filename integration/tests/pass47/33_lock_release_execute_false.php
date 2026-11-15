<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;$p->active=false;
$s=new Pass47Statement;$s->column=1;$u=new Pass47Statement;$u->ok=false;$p->queue=[$s,$u];$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:abc123abc123:staff:1']);fails47(fn()=>$m->releaseAll(),'could not be released');p47(count($m->held())===1,'lost unreleased lock');
echo 'PASS47 33 lock_release_execute_false OK'.PHP_EOL;
