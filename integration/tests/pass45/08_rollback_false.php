<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->rollbackOk=false;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>throw new DomainException('write failed')),'rollback failed');pass45_assert($p->rollbacks===1,'rollback not attempted');
echo "pass45 08_rollback_false OK\n";
