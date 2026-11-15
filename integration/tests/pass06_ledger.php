<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$db->fetchRow=['fingerprint'=>str_repeat('a',64),'state'=>'completed','booking_id'=>42];$db->beginTransaction();$ledger=new BookingRequestLedger($db);$row=$ledger->getForUpdate(str_repeat('x',24));a($row['booking_id']===42,'locked row loaded');a(str_contains($db->calls[0][0],'FOR UPDATE'),'locking read');$ledger->claim(str_repeat('x',24),str_repeat('b',64));a(str_contains($db->calls[1][0],'INSERT INTO titan_booking_requests'),'new request claimed');fails(fn()=>$ledger->claim('bad',str_repeat('a',64)),'bad key');
echo "pass06_ledger.php OK\n";
