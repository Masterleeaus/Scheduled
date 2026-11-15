<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$p=new Pass47PDO;
$s=new Pass47Statement;$s->ok=false;$p->queue=[$s];fails47(fn()=>\Titan\BookingSidecar\BookingWriteProofReader::read($p,27),'Booking proof query failed');
echo 'PASS47 27 proof_booking_execute_false OK'.PHP_EOL;
