<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$db->lastId='54';$cmd=sampleBooking();$id=(new BookingLegacyAppointmentWriter($db))->insert(17,3,$cmd,'45.00');a($id===54,'appointment id');a(str_contains($db->calls[0][0],'ct_bookings'),'correct table');a(str_contains($db->calls[0][0],'booking_status'),'status set');a(end($db->calls[0][1])==='2,5','canonical staff csv');fails(fn()=>(new BookingLegacyAppointmentWriter($db))->insert(-1,3,$cmd,'45.00'),'bad order rejected');
echo "pass06_appointment_writer.php OK\n";
