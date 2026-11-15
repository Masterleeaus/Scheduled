<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCommand,BookingSchedulePolicy,BookingStateMachine,BookingTimeWindow,BookingRecurrenceRule,BookingRecurrencePlan,BookingKey,BookingMoney,BookingCustomer,BookingStaff,BookingService,BookingReservationPolicy,BookingResult,BookingAuditEvent,BookingIdempotency,BookingCancellation,BookingReschedule,BookingQuote,BookingPayloadMapper};
function ok($value,$message){if(!$value)throw new RuntimeException($message);}
$r=BookingAuditEvent::record('confirmed','tenant-test',3,'my-key');ok(!str_contains(json_encode($r),'my-key'),'hashed key');ok(BookingResult::response(3,'C','2026-11-10 09:00:00')['booking_id']===3,'result');
echo "pass05_audit OK\n";
