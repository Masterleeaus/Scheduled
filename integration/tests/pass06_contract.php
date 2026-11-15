<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
require __DIR__.'/pass06_assert.php';
use Titan\BookingSidecar\{BookingWriteSchema,BookingLockKeys,BookingLockManager,BookingTransaction,BookingRequestLedger,BookingOrderSequence,BookingLegacyClientWriter,BookingLegacyAppointmentWriter,BookingLegacyStatusWriter,BookingLegacyBookingReader,BookingConflictGuard,BookingServiceRateGuard,BookingWriteFeatureGate,BookingWriteCoordinator,BookingLegacySqlContract,BookingCommand,BookingIdempotency};
$columns=BookingLegacySqlContract::requiredColumns();a(isset($columns['ct_bookings']),'original booking table');a(in_array('ct_order_client_info',BookingLegacySqlContract::requiredTables(),true),'client table exists');a(count(BookingLegacySqlContract::validate($columns))===0,'complete schema inventory');unset($columns['ct_bookings'][0]);a(in_array('ct_bookings.order_id',BookingLegacySqlContract::validate($columns),true),'missing field detected');
echo "pass06_contract.php OK\n";
