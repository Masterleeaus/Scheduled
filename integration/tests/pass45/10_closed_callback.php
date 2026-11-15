<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;pass45_throws(fn()=>\App\Booking\Public\Redemption\RedeemTransaction::run($p,function()use($p){$p->active=false;return 77;}),'unexpectedly closed');pass45_assert($p->commits===0,'committed closed transaction');
echo "pass45 10_closed_callback OK\n";
