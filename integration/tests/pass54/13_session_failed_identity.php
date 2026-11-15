<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->queryFail=true;check54(fn()=>Titan\BookingIntegration\SessionSafetyProbe::assert($db),true,'session identity failed');
echo "PASS54 13 session_failed_identity OK\n";
