<?php
declare(strict_types=1);
require __DIR__.'/support.php';
use Titan\BookingIntegration\{AcceptanceReadbackSql,LifecycleCommittedReader,LifecycleFullBookingReader,LifecycleClientOrderReader,ConcurrencyBookingReadback,LifecycleBookingResult,LifecycleResultIdentity};
p61_assert(LifecycleBookingResult::created(['status'=>'active','replayed'=>false,'booking_id'=>2147483647])===2147483647,'max valid ID');
echo "PASS61 21_create_valid_id\n";
