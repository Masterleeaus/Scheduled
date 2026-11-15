<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->execValue=0;fails47(fn()=>(new \Titan\BookingSidecar\BookingOrderSequence($p))->next(),'allocation insert failed');
echo 'PASS47 14 order_insert_zero OK'.PHP_EOL;
