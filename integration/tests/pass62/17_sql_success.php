<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_ok(LifecycleFootprintSql::count(new P62PDO([['total'=>'12']]),LifecycleFootprintSql::BOOKINGS)===12, '17_sql_success');
echo "PASS62 17_sql_success\n";
