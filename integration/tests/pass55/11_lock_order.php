<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:staff:9','tb:staff:1','tb:staff:5']);a55($m->held()===['tb:staff:1','tb:staff:5','tb:staff:9'],'nondeterministic lock order');
echo 'pass55 11_lock_order OK\n';
