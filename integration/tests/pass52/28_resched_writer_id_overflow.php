<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$db=db51();fail52(fn()=>(new \Titan\BookingSidecar\BookingRescheduleWriter($db))->update(2147483648,'C',['local_start'=>'2026-11-12 10:00:00'],[2]),'out of range');
echo 'PASS52 28_resched_writer_id_overflow OK';
