<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$db->fetchRows=[['rates'=>'55.00']];fails(fn()=>(new BookingServiceRateGuard($db))->confirmedRate(['method_unit_id'=>4]),'unverified rate rules denied');a(count($db->calls)===0,'unknown pricing settings rejected before querying');fails(fn()=>(new BookingServiceRateGuard($db))->confirmedRate(['method_unit_id'=>0]),'reject invalid unit');
echo "pass06_rate_fail_closed.php OK\n";
