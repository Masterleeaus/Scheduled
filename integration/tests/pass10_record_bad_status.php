<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
try{BookingMutationRecord::parse(['id'=>3,'order_id'=>8,'booking_status'=>'XYZ','staff_ids'=>'2']);throw new Exception('accepted');}catch(DomainException $e){}
echo "pass10 record_bad_status ok\n";
