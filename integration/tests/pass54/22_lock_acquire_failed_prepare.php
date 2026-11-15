<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->prepareFail=true;check54(fn()=>Titan\BookingIntegration\AdvisoryLockProbe::run($db),true,'lock preparation');
echo "PASS54 22 lock_acquire_failed_prepare OK\n";
