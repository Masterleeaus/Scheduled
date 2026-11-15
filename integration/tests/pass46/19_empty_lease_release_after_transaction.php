<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$lease=new \Titan\BookingSidecar\BookingSharedLockLease($p);$lease->release();check46($lease->held()===[],'empty locks wrong');
echo "pass46 19 empty_lease_release_after_transaction OK\n";
