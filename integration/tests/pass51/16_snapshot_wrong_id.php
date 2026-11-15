<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleRecordReader(db51([row51(8)])))->get(7),'Wrong booking');
echo 'PASS51 16_snapshot_wrong_id OK';
