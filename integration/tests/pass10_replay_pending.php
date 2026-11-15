<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
try{BookingMutationReplay::evaluate(['fingerprint'=>str_repeat('a',64),'state'=>'pending'],str_repeat('a',64));throw new Exception('accepted');}catch(DomainException $e){}
echo "pass10 replay_pending ok\n";
