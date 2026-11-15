<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->active=true;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>1),'Nested');pass45_assert($p->begins===0,'nested transaction started');
echo "pass45 02_nested OK\n";
