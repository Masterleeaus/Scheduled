<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$r=BookingCancellationInput::parse(['booking_id'=>4,'idempotency_key'=>str_repeat('a',24),'reason'=>'changed']);assert($r['booking_id']===4);
echo "pass10 cancel_input_ok ok\n";
