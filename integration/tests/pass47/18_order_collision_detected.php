<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->column=1;$p->queue=[$s];fails47(fn()=>(new \Titan\BookingSidecar\BookingOrderSequence($p))->next(),'not provisioned');
echo 'PASS47 18 order_collision_detected OK'.PHP_EOL;
