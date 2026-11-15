<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=new P54Pdo();$db->queryFail=true;check54(fn()=>Titan\BookingIntegration\IsolationSentinel::assert($db,'titan_booking_test_qa'),true,'isolation query failed');
echo "PASS54 14 isolation_failed_identity OK\n";
