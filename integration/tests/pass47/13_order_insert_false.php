<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->execValue=false;fails47(fn()=>(new \Titan\BookingSidecar\BookingOrderSequence($p))->next(),'allocation insert failed');
echo 'PASS47 13 order_insert_false OK'.PHP_EOL;
