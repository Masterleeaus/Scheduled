<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$r=BookingCancellationReadiness::check(array_fill_keys(['schema_verified','ownership_verified','legacy_status_verified','concurrency_verified','database_tested'],true));assert($r['ready']&&!$r['writes_enabled']);
echo "pass10 readiness_full ok\n";
