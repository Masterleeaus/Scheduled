<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->beginThrows=true;reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>1),'begin exception');check46($p->commits===0,'begin threw then committed');
echo "pass46 04 begin_exception OK\n";
