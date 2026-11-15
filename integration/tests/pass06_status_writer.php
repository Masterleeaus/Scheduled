<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$db->beginTransaction();$db->affected=1;(new BookingLegacyStatusWriter($db))->cancel(5,'A','CC','customer request');a($db->calls[0][1][0]==='CC','cancel status');a($db->calls[0][1][3]==='A','optimistic check');fails(fn()=>(new BookingLegacyStatusWriter($db))->cancel(5,'CC','A',''),'invalid transition');$db->affected=0;fails(fn()=>(new BookingLegacyStatusWriter($db))->cancel(5,'A','CC',''),'lost update detected');
echo "pass06_status_writer.php OK\n";
