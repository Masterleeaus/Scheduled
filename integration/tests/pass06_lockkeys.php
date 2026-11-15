<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
a(BookingLockKeys::forStaff([11,2,2])===['tb:staff:11','tb:staff:2'],'stable sorted lock keys');a(BookingLockKeys::forBooking(9)==='tb:booking:9','booking lock');fails(fn()=>BookingLockKeys::forBooking(0),'invalid booking lock');fails(fn()=>BookingLockKeys::forStaff(['2']),'reject untyped staff');
echo "pass06_lockkeys.php OK\n";
