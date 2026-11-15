<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_ok(LifecycleFootprintEvidence::create(new P62PDO([['total'=>'3']]),['bookings'=>2,'orders'=>2])===['bookings'=>3,'orders'=>3], '30_evidence_create');
echo "PASS62 30_evidence_create\n";
