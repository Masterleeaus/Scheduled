<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleDurationWriter(db51([],false)))->update(11,60),'update failed');
echo 'PASS51 22_duration_update_failed OK';
