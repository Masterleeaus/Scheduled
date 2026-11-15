<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_reject(fn()=>ConcurrencyBookingReadback::read(new Pass61PDO([['id'=>1],['id'=>1]]),1),'winner duplicate');
echo "PASS61 17_winner_duplicate\n";
