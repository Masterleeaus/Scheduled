<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_ok(LifecycleWriteFootprint::created(['bookings'=>3,'orders'=>6],['bookings'=>4,'orders'=>7])===null, '20_delta_create');
echo "PASS62 20_delta_create\n";
