<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$p->failPrepare=true;fails47(fn()=>\Titan\BookingSidecar\BookingWriteProofReader::read($p,27),'Booking proof query failed');
echo 'PASS47 26 proof_booking_prepare_false OK'.PHP_EOL;
