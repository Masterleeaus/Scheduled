<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
try{BookingMutationActor::normalize(['role'=>'staff','id'=>7]);throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "pass10 actor_invalid ok\n";
