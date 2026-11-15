<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCommand,BookingSchedulePolicy,BookingStateMachine,BookingTimeWindow,BookingRecurrenceRule,BookingRecurrencePlan,BookingKey,BookingMoney,BookingCustomer,BookingStaff,BookingService,BookingReservationPolicy,BookingResult,BookingAuditEvent,BookingIdempotency,BookingCancellation,BookingReschedule,BookingQuote,BookingPayloadMapper};
function ok($value,$message){if(!$value)throw new RuntimeException($message);}
ok(BookingIdempotency::decide(null,str_repeat('a',64))==='new','new');ok(BookingIdempotency::decide(['fingerprint'=>str_repeat('a',64),'status'=>'completed'],str_repeat('a',64))==='replay','replay');try{BookingIdempotency::decide(['fingerprint'=>str_repeat('b',64),'status'=>'completed'],str_repeat('a',64));throw new RuntimeException('unsafe key reuse');}catch(DomainException $e){}
echo "pass05_idempotency OK\n";
