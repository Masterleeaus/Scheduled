<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$db->lastId='39';$id=(new BookingLegacyClientWriter($db))->insert(7,['name'=>'Jane Test','email'=>'jane@example.test','phone'=>'+61412345678'],90);a($id===39,'client id');a(str_contains($db->calls[0][0],'ct_order_client_info'),'correct source table');a($db->calls[0][1][5]===90,'duration persisted');fails(fn()=>(new BookingLegacyClientWriter($db))->insert(0,['name'=>'A','email'=>'a@b.test','phone'=>'12345'],30),'invalid order rejected');
echo "pass06_client_writer.php OK\n";
