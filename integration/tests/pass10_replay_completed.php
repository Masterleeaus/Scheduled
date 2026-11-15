<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$f=str_repeat('a',64);assert(BookingMutationReplay::evaluate(['fingerprint'=>$f,'state'=>'completed','booking_id'=>5],$f)===5);
echo "pass10 replay_completed ok\n";
