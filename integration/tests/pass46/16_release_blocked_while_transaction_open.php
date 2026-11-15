<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass46PDO;$p->active=true;$lease=new \Titan\BookingSidecar\BookingSharedLockLease($p);reject46(fn()=>$lease->release(),'after booking transaction');
echo "pass46 16 release_blocked_while_transaction_open OK\n";
