<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
rejects51(fn()=>(new \Titan\BookingSidecar\BookingRescheduleDurationWriter(db51([],true,0)))->update(11,60),'exactly one row');
echo 'PASS51 23_duration_update_zero OK';
