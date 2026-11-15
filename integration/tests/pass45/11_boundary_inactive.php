<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;pass45_throws(fn()=>\App\Booking\Public\Redemption\CommitTransactionBoundary::beforeWrite($p),'outside transaction');
echo "pass45 11_boundary_inactive OK\n";
