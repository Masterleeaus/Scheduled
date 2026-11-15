<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->startOk=false;check54(fn()=>Titan\BookingIntegration\RollbackProbe::run($db),true,'transaction start false');
echo "PASS54 17 transaction_start_failure OK\n";
