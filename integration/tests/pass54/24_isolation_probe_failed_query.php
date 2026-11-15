<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->queryFail=true;check54(fn()=>Titan\BookingIntegration\TransactionIsolationProbe::inspect($db),true,'isolation query false');
echo "PASS54 24 isolation_probe_failed_query OK\n";
