<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$lease=new \Titan\BookingSidecar\BookingSharedLockLease($p);check46(count($lease->held())===0,'new lease held locks');
echo "pass46 20 lease_held_list_is_empty_initially OK\n";
