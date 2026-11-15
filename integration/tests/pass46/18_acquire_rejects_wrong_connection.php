<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$other=new Pass46PDO;$lease=new \Titan\BookingSidecar\BookingSharedLockLease($p);reject46(fn()=>$lease->acquire($other,['staff_id'=>1]),'connection mismatch');
echo "pass46 18 acquire_rejects_wrong_connection OK\n";
