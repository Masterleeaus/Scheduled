<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleFootprintDelta::assert(['bookings'=>PHP_INT_MAX,'orders'=>0],['bookings'=>PHP_INT_MAX,'orders'=>1],1,1), '24_delta_overflow');
echo "PASS62 24_delta_overflow\n";
