<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleFootprintSql::count(new P62PDO([],false),LifecycleFootprintSql::BOOKINGS), '12_sql_prepare_false');
echo "PASS62 12_sql_prepare_false\n";
