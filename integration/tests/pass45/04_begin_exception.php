<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->throwBegin=true;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>1),'begin threw');pass45_assert($p->commits===0,'begin throw proceeded');
echo "pass45 04_begin_exception OK\n";
