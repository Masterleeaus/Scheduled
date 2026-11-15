<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->ok=false;$p->queue=[$s];fails47(fn()=>(new \Titan\BookingSidecar\BookingLegacyClientWriter($p))->insert(2,customer47(),60),'Customer order insert failed');
echo 'PASS47 21 customer_execute_false OK'.PHP_EOL;
