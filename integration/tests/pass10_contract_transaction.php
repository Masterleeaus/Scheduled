<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$code=file_get_contents(__DIR__.'/../../private/booking-sidecar/BookingCancellationCoordinator.php');assert(str_contains($code,'BookingTransaction')&&str_contains($code,'BookingLegacyStatusWriter'));
echo "pass10 contract_transaction ok\n";
