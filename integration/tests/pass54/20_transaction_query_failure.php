<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->queryFail=true;check54(fn()=>Titan\BookingIntegration\RollbackProbe::run($db),true,'rollback on bad read');if($db->transaction)throw new RuntimeException('rollback was not attempted');
echo "PASS54 20 transaction_query_failure OK\n";
