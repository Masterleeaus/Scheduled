<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>throw new LogicException('reject')),'reject');pass45_assert($p->commits===0&&$p->rollbacks===1,'unexpected commit');
echo "pass45 19_no_commit_on_exception OK\n";
