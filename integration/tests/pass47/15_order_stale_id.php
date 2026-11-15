<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->insertId='0';fails47(fn()=>(new \Titan\BookingSidecar\BookingOrderSequence($p))->next(),'Unable to allocate order');
echo 'PASS47 15 order_stale_id OK'.PHP_EOL;
