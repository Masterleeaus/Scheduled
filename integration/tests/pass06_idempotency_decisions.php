<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$hash=str_repeat('a',64);a(BookingIdempotency::decide(null,$hash)==='new','first request');a(BookingIdempotency::decide(['fingerprint'=>$hash,'status'=>'completed'],$hash)==='replay','completed replay');a(BookingIdempotency::decide(['fingerprint'=>$hash,'status'=>'pending'],$hash)==='pending','pending request');fails(fn()=>BookingIdempotency::decide(['fingerprint'=>str_repeat('b',64),'status'=>'completed'],$hash),'payload mutation blocked');
echo "pass06_idempotency_decisions.php OK\n";
