<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass45PDO;pass45_throws(fn()=>\Titan\BookingSidecar\BookingAtomicPersistBoundary::assert($p),'closed');
echo "pass45 16_writer_boundary_closed OK\n";
