<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleFootprintSnapshot::read(new P62PDO([['total'=>'2']],true,true,true)), '18_boundary_in_transaction');
echo "PASS62 18_boundary_in_transaction\n";
