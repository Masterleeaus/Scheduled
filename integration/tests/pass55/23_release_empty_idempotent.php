<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->releaseAll();a55(!$p->queries&&!$m->held(),'empty release performed SQL');
echo 'pass55 23_release_empty_idempotent OK\n';
