<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$r=BookingCancellationReadiness::check(['schema_verified'=>true]);assert(!$r['ready']&&in_array('database_tested',$r['missing'],true));
echo "pass10 readiness_missing ok\n";
