<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->beginOk=false;reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>1),'could not start');check46($p->commits===0,'false begin committed');
echo "pass46 03 begin_false OK\n";
