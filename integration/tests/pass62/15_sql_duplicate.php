<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleFootprintSql::count(new P62PDO([['total'=>'1'],['total'=>'2']]),LifecycleFootprintSql::BOOKINGS), '15_sql_duplicate');
echo "PASS62 15_sql_duplicate\n";
