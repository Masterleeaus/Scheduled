<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->active=true; reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>1),'Nested');check46($p->begins===0,'nested touched state');
echo "pass46 02 nested_transaction OK\n";
