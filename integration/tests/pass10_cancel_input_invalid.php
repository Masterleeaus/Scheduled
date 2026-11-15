<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
try{BookingCancellationInput::parse(['booking_id'=>0,'idempotency_key'=>str_repeat('a',24)]);throw new Exception('accepted');}catch(InvalidArgumentException $e){}
echo "pass10 cancel_input_invalid ok\n";
