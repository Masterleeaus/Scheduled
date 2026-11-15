<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_reject(fn()=>LifecycleCommittedReader::read(new Pass61PDO([]),1),'committed missing');
echo "PASS61 09_committed_missing\n";
