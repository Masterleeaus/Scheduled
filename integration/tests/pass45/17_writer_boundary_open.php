<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;$p->active=true;\Titan\BookingSidecar\BookingAtomicPersistBoundary::assert($p);
echo "pass45 17_writer_boundary_open OK\n";
