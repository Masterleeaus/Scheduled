<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCommand,BookingSchedulePolicy,BookingStateMachine,BookingTimeWindow,BookingRecurrenceRule,BookingRecurrencePlan,BookingKey,BookingMoney,BookingCustomer,BookingStaff,BookingService,BookingReservationPolicy,BookingResult,BookingAuditEvent,BookingIdempotency,BookingCancellation,BookingReschedule,BookingQuote,BookingPayloadMapper};
function ok($value,$message){if(!$value)throw new RuntimeException($message);}
ok(BookingStateMachine::transition('A','C')==='C','confirm');ok(!BookingStateMachine::occupiesStaff('CC'),'cancel frees');try{BookingStateMachine::transition('CO','A');throw new RuntimeException('allowed terminal');}catch(DomainException $e){}
echo "pass05_state OK\n";
