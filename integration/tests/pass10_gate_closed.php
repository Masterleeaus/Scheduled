<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
try{BookingBookingLifecycleGuard::assertReady([],[]);throw new Exception('accepted');}catch(RuntimeException $e){}
echo "pass10 gate_closed ok\n";
