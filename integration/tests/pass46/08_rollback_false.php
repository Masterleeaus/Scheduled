<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->rollbackOk=false;reject46(fn()=>(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>throw new DomainException('original')),'rollback failed');check46($p->rollbacks===1,'rollback false not detected');
echo "pass46 08 rollback_false OK\n";
