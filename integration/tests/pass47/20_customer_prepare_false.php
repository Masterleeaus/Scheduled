<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->failPrepare=true;fails47(fn()=>(new \Titan\BookingSidecar\BookingLegacyClientWriter($p))->insert(2,customer47(),60),'Customer order insert failed');
echo 'PASS47 20 customer_prepare_false OK'.PHP_EOL;
