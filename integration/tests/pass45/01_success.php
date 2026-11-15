<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO; $v=\App\Booking\Public\Redemption\RedeemTransaction::run($p,fn()=>37);pass45_assert($v===37&&$p->begins===1&&$p->commits===1&&$p->rollbacks===0&&!$p->active,'successful commit');
echo "pass45 01_success OK\n";
