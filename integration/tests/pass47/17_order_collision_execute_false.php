<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->ok=false;$p->queue=[$s];fails47(fn()=>(new \Titan\BookingSidecar\BookingOrderSequence($p))->next(),'collision check failed');
echo 'PASS47 17 order_collision_execute_false OK'.PHP_EOL;
