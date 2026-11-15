<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCommand,BookingSchedulePolicy,BookingStateMachine,BookingTimeWindow,BookingRecurrenceRule,BookingRecurrencePlan,BookingKey,BookingMoney,BookingCustomer,BookingStaff,BookingService,BookingReservationPolicy,BookingResult,BookingAuditEvent,BookingIdempotency,BookingCancellation,BookingReschedule,BookingQuote,BookingPayloadMapper};
function ok($value,$message){if(!$value)throw new RuntimeException($message);}
$dates=BookingRecurrencePlan::dates('2026-11-10 09:00:00','Australia/Melbourne',['frequency'=>'fortnightly','count'=>3]);ok(count($dates)===3,'count');ok($dates[1]==='2026-11-24 09:00:00','fortnight');
echo "pass05_recurrence OK\n";
