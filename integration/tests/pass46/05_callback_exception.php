<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>throw new DomainException('invalid booking')),'invalid booking');check46($p->rollbacks===1&&$p->commits===0,'callback failure not rolled back');
echo "pass46 05 callback_exception OK\n";
