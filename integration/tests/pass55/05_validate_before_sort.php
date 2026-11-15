<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1',7]),'Invalid lock key');a55(!$p->queries,'partial lock acquired for invalid key');
echo 'pass55 05_validate_before_sort OK\n';
