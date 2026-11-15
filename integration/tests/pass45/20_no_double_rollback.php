<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->commitOk=false;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>0),'commit failed');pass45_assert($p->rollbacks===1,'duplicate rollback');
echo "pass45 20_no_double_rollback OK\n";
