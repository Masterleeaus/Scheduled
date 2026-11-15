<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleWriteFootprint::created(['bookings'=>3,'orders'=>6],['bookings'=>5,'orders'=>7]), '21_delta_extra_booking');
echo "PASS62 21_delta_extra_booking\n";
