<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->failPrepare=true;fails47(fn()=>(new \Titan\BookingSidecar\BookingOrderSequence($p))->next(),'collision check failed');
echo 'PASS47 16 order_collision_prepare_rejected OK'.PHP_EOL;
