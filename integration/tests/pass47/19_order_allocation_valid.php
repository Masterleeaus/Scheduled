<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->column=false;$p->queue=[$s];p47((new \Titan\BookingSidecar\BookingOrderSequence($p))->next()===27,'allocation should return allocated id');
echo 'PASS47 19 order_allocation_valid OK'.PHP_EOL;
