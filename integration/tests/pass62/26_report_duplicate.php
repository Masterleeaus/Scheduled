<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>(function(){ $r=new LifecycleFootprintReport();$r->mark('create');$r->mark('create');})(), '26_report_duplicate');
echo "PASS62 26_report_duplicate\n";
