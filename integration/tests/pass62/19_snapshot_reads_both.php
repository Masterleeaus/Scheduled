<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_ok(LifecycleFootprintSnapshot::read(new P62PDO([['total'=>'2']]))===['bookings'=>2,'orders'=>2], '19_snapshot_reads_both');
echo "PASS62 19_snapshot_reads_both\n";
