<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1'],31),'timeout');a55(!$p->queries,'SQL executed with invalid timeout');
echo 'pass55 22_reject_invalid_timeout OK\n';
