<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$db=new FakePDO();$db->scalarBySql['GET_LOCK']=0;$locks=new BookingLockManager($db);fails(fn()=>$locks->acquire(['tb:staff:3']),'timeout refuses');a($locks->held()===[],'failed acquisition not tracked');fails(fn()=>$locks->acquire(['evil']),'invalid lock rejected');
echo "pass06_lock_timeout.php OK\n";
