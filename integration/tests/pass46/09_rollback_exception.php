<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->rollbackThrows=true;reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>throw new DomainException('original')),'rollback failed');check46($p->rollbacks===1,'rollback exception not detected');
echo "pass46 09 rollback_exception OK\n";
