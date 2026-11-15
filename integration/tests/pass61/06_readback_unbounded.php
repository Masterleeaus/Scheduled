<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_reject(fn()=>AcceptanceReadbackSql::exactlyOne(new Pass61PDO([['id'=>1]]),'SELECT a',[],'test'),'unbounded query');
echo "PASS61 06_readback_unbounded\n";
