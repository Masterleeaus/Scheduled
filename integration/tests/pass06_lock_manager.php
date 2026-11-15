<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$db->scalarBySql['GET_LOCK']=1;$db->scalarBySql['RELEASE_LOCK']=1;$locks=new BookingLockManager($db);$locks->acquire(['tb:staff:5','tb:staff:3']);a($locks->held()===['tb:staff:3','tb:staff:5'],'ordered acquisition');$locks->acquire(['tb:staff:3']);a(count($locks->held())===2,'no repeat lock');$locks->releaseAll();a($locks->held()===[],'fully released');a(count($db->calls)===4,'two acquire two release');
echo "pass06_lock_manager.php OK\n";
