<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_ok(LifecycleFootprintNumber::parse((string)PHP_INT_MAX)===PHP_INT_MAX, '03_count_max');
echo "PASS62 03_count_max\n";
