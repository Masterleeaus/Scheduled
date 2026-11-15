<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$v=\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>['booking_id'=>73,'confirmed'=>false]);pass45_assert($v['booking_id']===73&&!$v['confirmed'],'return changed');
echo "pass45 18_return_value OK\n";
