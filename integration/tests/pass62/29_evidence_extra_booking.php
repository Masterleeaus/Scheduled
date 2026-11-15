<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{LifecycleFootprintNumber,LifecycleFootprintSql,LifecycleFootprintSnapshot,LifecycleWriteFootprint,LifecycleFootprintDelta,LifecycleFootprintReport,LifecycleFootprintEvidence};
p62_fail(fn()=>LifecycleFootprintEvidence::unchanged(new P62PDO([['total'=>'5']]),['bookings'=>4,'orders'=>5]), '29_evidence_extra_booking');
echo "PASS62 29_evidence_extra_booking\n";
