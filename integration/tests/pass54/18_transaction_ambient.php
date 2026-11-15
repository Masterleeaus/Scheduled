<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->transaction=true;check54(fn()=>Titan\BookingIntegration\RollbackProbe::run($db),true,'ambient transaction');
echo "PASS54 18 transaction_ambient OK\n";
