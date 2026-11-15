<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$t=new BookingTransaction($db);fails(fn()=> $t->run(function(){throw new RuntimeException('test rollback');}),'callback throws');a($db->rollbackCalls===1,'rollback invoked');a($db->commitCalls===0,'never committed');$db->beginTransaction();fails(fn()=>$t->run(fn()=>null),'nested tx refused');
echo "pass06_tx_rollback.php OK\n";
