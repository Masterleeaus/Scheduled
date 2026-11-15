<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
a(!BookingWriteFeatureGate::enabled([]),'off by default');a(!BookingWriteFeatureGate::enabled(['booking_writes_enabled'=>true]),'incomplete config denied');a(!BookingWriteFeatureGate::enabled(['booking_writes_enabled'=>'true','booking_schema_verified'=>true,'booking_rate_mapping_verified'=>true]),'string not accepted');a(BookingWriteFeatureGate::enabled(['booking_writes_enabled'=>true,'booking_schema_verified'=>true,'booking_rate_mapping_verified'=>true,'booking_concurrency_verified'=>true]),'explicit all verified');fails(fn()=>BookingWriteFeatureGate::requireEnabled([]),'gate throws');
echo "pass06_feature_gate.php OK\n";
