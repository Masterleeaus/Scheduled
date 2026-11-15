<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleFootprintSql::count(new P62PDO([],true,false),LifecycleFootprintSql::ORDERS), '13_sql_execute_false');
echo "PASS62 13_sql_execute_false\n";
