<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();check54(fn()=>Titan\BookingIntegration\RollbackProbe::run($db),false,'rollback success');if($db->transaction)throw new RuntimeException('transaction not closed');
echo "PASS54 21 transaction_success OK\n";
