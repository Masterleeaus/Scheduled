<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>throw new DomainException('callback failed')),'callback failed');pass45_assert($p->rollbacks===1&&$p->commits===0,'callback failure did not rollback');
echo "pass45 05_callback_exception OK\n";
