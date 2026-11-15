<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$code=file_get_contents(__DIR__.'/../../private/booking-sidecar/BookingMutationCustomerScope.php');assert(str_contains($code,'id=? AND client_id=?'));
echo "pass10 contract_ownership ok\n";
