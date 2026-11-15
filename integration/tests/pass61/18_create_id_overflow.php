<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_reject(fn()=>LifecycleBookingResult::created(['status'=>'active','replayed'=>false,'booking_id'=>2147483648]),'create overflow');
echo "PASS61 18_create_id_overflow\n";
