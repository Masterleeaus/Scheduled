<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$t=new BookingTransaction($db);$r=$t->run(function($pdo){a($pdo->inTransaction(),'active inside callback');return 23;});a($r===23,'return value');a($db->commitCalls===1,'committed once');a($db->rollbackCalls===0,'not rolled back');
echo "pass06_tx_success.php OK\n";
