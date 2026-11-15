<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=db51();fail52(fn()=>(new \Titan\BookingSidecar\BookingLegacyStatusWriter($db))->cancel(2147483648,'A','CC','reason'),'out of range');
echo 'PASS52 27_writer_id_overflow OK';
