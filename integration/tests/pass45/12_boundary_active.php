<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->active=true;\App\Booking\Public\Redemption\CommitTransactionBoundary::beforeWrite($p);\App\Booking\Public\Redemption\CommitTransactionBoundary::afterWrite($p);
echo "pass45 12_boundary_active OK\n";
