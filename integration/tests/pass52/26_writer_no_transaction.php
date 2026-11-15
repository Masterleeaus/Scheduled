<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=db51([],true,1,false);fail52(fn()=>(new \Titan\BookingSidecar\BookingLegacyStatusWriter($db))->cancel(7,'A','CC','reason'),'transaction');
echo 'PASS52 26_writer_no_transaction OK';
