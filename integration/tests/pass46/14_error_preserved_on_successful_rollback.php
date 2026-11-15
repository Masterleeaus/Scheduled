<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;try{(new \Titan\BookingSidecar\BookingTransaction($p))->run(fn()=>throw new DomainException('original fault'));}catch(DomainException $e){check46($e->getMessage()==='original fault'&&$p->rollbacks===1,'original error not preserved');exit(0);}throw new RuntimeException('missing original error');
echo "pass46 14 error_preserved_on_successful_rollback OK\n";
