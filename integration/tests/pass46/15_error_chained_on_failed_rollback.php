<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->rollbackOk=false;try{(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>throw new DomainException('original fault'));}catch(RuntimeException $e){check46($e->getPrevious() instanceof DomainException,'original not chained');exit(0);}throw new RuntimeException('failed rollback not detected');
echo "pass46 15 error_chained_on_failed_rollback OK\n";
