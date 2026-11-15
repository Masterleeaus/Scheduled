<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_assert(AcceptanceReadbackSql::exactlyOne(new Pass61PDO([['id'=>5]]),'SELECT a LIMIT 2',[],'test')['id']===5,'single success');
echo "PASS61 07_readback_one\n";
