<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_assert(AcceptanceReadbackSql::exactlyOne(new Pass61PDO([['id'=>2]]),'SELECT a limit   2  ',[],'test')['id']===2,'whitespace limit');
echo "PASS61 08_readback_whitespace_limit\n";
