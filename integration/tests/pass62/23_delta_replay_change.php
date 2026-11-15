<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleWriteFootprint::unchanged(['bookings'=>3,'orders'=>6],['bookings'=>3,'orders'=>7]), '23_delta_replay_change');
echo "PASS62 23_delta_replay_change\n";
