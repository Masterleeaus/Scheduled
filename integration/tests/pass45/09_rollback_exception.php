<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->throwRollback=true;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>throw new DomainException('write failed')),'rollback failed');pass45_assert($p->rollbacks===1,'rollback not attempted');
echo "pass45 09_rollback_exception OK\n";
