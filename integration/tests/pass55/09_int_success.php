<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:staff:1']);a55($m->held()===['tb:staff:1'],'valid int lock rejected');$m->releaseAll();a55(!$m->held(),'release failed');
echo 'pass55 09_int_success OK\n';
