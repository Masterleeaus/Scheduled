<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->throwCommit=true;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>1),'commit threw');pass45_assert($p->rollbacks===1,'commit throw did not rollback');
echo "pass45 07_commit_exception OK\n";
