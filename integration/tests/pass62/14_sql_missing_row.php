<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleFootprintSql::count(new P62PDO([]),LifecycleFootprintSql::BOOKINGS), '14_sql_missing_row');
echo "PASS62 14_sql_missing_row\n";
