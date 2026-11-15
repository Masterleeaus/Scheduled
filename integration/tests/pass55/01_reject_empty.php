<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);reject55(fn()=>$m->acquire([]),'cannot be empty');a55(!$p->queries,'SQL executed for empty lock set');
echo 'pass55 01_reject_empty OK\n';
