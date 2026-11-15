<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCommand,BookingSchedulePolicy,BookingStateMachine,BookingTimeWindow,BookingRecurrenceRule,BookingRecurrencePlan,BookingKey,BookingMoney,BookingCustomer,BookingStaff,BookingService,BookingReservationPolicy,BookingResult,BookingAuditEvent,BookingIdempotency,BookingCancellation,BookingReschedule,BookingQuote,BookingPayloadMapper};
function ok($value,$message){if(!$value)throw new RuntimeException($message);}
$c=BookingCustomer::parse(['name'=>'Sam','email'=>'sam@example.test','phone'=>'+61412345678']);ok($c['name']==='Sam','name');try{BookingCustomer::parse(['name'=>'S','email'=>'invalid','phone'=>'123']);throw new RuntimeException('missed invalid');}catch(InvalidArgumentException $e){}
echo "pass05_customer OK\n";
