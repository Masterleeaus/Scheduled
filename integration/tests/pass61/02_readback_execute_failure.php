<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_reject(fn()=>AcceptanceReadbackSql::exactlyOne(new Pass61PDO([],true,false),'SELECT a LIMIT 2',[],'test'),'execute false');
echo "PASS61 02_readback_execute_failure\n";
