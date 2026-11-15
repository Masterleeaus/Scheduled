<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleRecordReader(db51([],false)))->get(7),'query failed');
echo 'PASS51 18_snapshot_failed_sql OK';
