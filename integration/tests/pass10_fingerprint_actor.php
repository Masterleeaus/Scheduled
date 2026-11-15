<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$a=BookingMutationFingerprint::of('cancel',['role'=>'owner','id'=>1],['booking_id'=>4]);$b=BookingMutationFingerprint::of('cancel',['role'=>'owner','id'=>2],['booking_id'=>4]);assert($a!==$b);
echo "pass10 fingerprint_actor ok\n";
