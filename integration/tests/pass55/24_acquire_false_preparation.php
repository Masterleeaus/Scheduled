<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$p->prepareFalse=true;$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire(['tb:staff:1']),'query failed');a55(!$m->held(),'failed prepare retained lock');
echo 'pass55 24_acquire_false_preparation OK\n';
