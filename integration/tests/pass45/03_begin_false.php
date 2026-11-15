<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->beginOk=false;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>1),'could not start');pass45_assert($p->commits===0&&$p->rollbacks===0,'begin false proceeded');
echo "pass45 03_begin_false OK\n";
