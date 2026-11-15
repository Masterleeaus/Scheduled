<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_reject(fn()=>AcceptanceReadbackSql::exactlyOne(new Pass61PDO(false),'SELECT a LIMIT 2',[],'test'),'fetch false');
echo "PASS61 03_readback_fetch_failure\n";
