<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCommand,BookingSchedulePolicy,BookingStateMachine,BookingTimeWindow,BookingRecurrenceRule,BookingRecurrencePlan,BookingKey,BookingMoney,BookingCustomer,BookingStaff,BookingService,BookingReservationPolicy,BookingResult,BookingAuditEvent,BookingIdempotency,BookingCancellation,BookingReschedule,BookingQuote,BookingPayloadMapper};
function ok($value,$message){if(!$value)throw new RuntimeException($message);}
$c=BookingReschedule::parse(['booking_id'=>12,'start_at'=>'2026-11-10 09:00:00','duration_minutes'=>90,'timezone'=>'Australia/Melbourne','staff_ids'=>[2],'idempotency_key'=>str_repeat('c',24)]);ok($c['window']['end']-$c['window']['start']===5400,'new duration');
echo "pass05_reschedule OK\n";
