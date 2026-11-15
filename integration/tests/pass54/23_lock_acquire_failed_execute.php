<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->stmt->executed=false;check54(fn()=>Titan\BookingIntegration\AdvisoryLockProbe::run($db),true,'lock statement false');
echo "PASS54 23 lock_acquire_failed_execute OK\n";
