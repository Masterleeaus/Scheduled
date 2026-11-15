<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->commitOk=false;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>1),'commit failed');pass45_assert($p->rollbacks===1,'false commit did not rollback');
echo "pass45 06_commit_false OK\n";
