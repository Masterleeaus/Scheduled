<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$statements=BookingWriteSchema::statements();a(count($statements)===4,'expected four migrations');a(str_contains($statements[0],'ENGINE=InnoDB'),'transactional orders');a(str_contains($statements[1],'PRIMARY KEY'),'unique request key');a(count(BookingWriteSchema::baseTables())===2,'two additive tables');a(count(BookingWriteSchema::optionalIndexes())===2,'two optional indexes');a(!str_contains(implode(' ',BookingWriteSchema::baseTables()),'DROP '),'no destructive migration');
echo "pass06_schema.php OK\n";
