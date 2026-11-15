<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$a=['role'=>'owner','id'=>1];$c=['booking_id'=>4];assert(BookingMutationFingerprint::of('cancel',$a,$c)===BookingMutationFingerprint::of('cancel',$a,$c));
echo "pass10 fingerprint_stable ok\n";
