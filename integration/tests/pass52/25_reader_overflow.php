<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
fail52(fn()=>(new \Titan\BookingSidecar\BookingMutationReader(db51([row52()])))->locked(2147483648),'Invalid booking ID');
echo 'PASS52 25_reader_overflow OK';
