<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->rollbackOk=false;check54(fn()=>Titan\BookingIntegration\RollbackProbe::run($db),true,'rollback false');
echo "PASS54 19 transaction_rollback_failure OK\n";
