<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass55PDO;$m=new \Titan\BookingSidecar\BookingLockManager($p);$m->acquire(['tb:staff:1','tb:staff:1']);$m->acquire(['tb:staff:1']);a55(count($p->queries)===1&&count($m->held())===1,'duplicate lock SQL');
echo 'pass55 12_duplicate_no_reentrant OK\n';
