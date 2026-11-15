<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_reject(fn()=>LifecycleCommittedReader::read(new Pass61PDO([],true,false),1),'committed execute');
echo "PASS61 11_committed_execute_fail\n";
