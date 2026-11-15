<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingMutationActor,BookingCancellationInput,BookingCancellationPolicy,BookingMutationRecord,BookingMutationFingerprint,BookingMutationReplay,BookingCancellationReadiness,BookingBookingLifecycleGuard};
$r=BookingMutationRecord::parse(['id'=>3,'order_id'=>8,'booking_status'=>'C','staff_ids'=>'2,4','booking_date_time'=>'2026-11-09 09:00:00']);assert($r['staff_ids']=== [2,4]);
echo "pass10 record_normalize ok\n";
