<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleRecordReader(db51([row51(),row51()])))->get(7),'Ambiguous');
echo 'PASS51 17_snapshot_duplicate OK';
