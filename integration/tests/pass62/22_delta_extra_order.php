<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleWriteFootprint::created(['bookings'=>3,'orders'=>6],['bookings'=>4,'orders'=>8]), '22_delta_extra_order');
echo "PASS62 22_delta_extra_order\n";
