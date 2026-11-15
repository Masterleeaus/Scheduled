<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_reject(fn()=>AcceptanceReadbackSql::exactlyOne(new Pass61PDO([]),'SELECT a LIMIT 2',[],'test'),'missing row');
echo "PASS61 04_readback_empty\n";
