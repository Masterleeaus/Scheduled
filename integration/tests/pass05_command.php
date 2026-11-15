<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingCommand,BookingSchedulePolicy,BookingStateMachine,BookingTimeWindow,BookingRecurrenceRule,BookingRecurrencePlan,BookingKey,BookingMoney,BookingCustomer,BookingStaff,BookingService,BookingReservationPolicy,BookingResult,BookingAuditEvent,BookingIdempotency,BookingCancellation,BookingReschedule,BookingQuote,BookingPayloadMapper};
function ok($value,$message){if(!$value)throw new RuntimeException($message);}
$c=BookingCommand::create(['idempotency_key'=>str_repeat('a',24),'staff_ids'=>[5,3,5],'service'=>['service_id'=>2,'method_id'=>4,'method_unit_id'=>1,'quantity'=>1],'customer'=>['name'=>'Test Client','email'=>'client@example.test','phone'=>'+61412345678'],'start_at'=>'2026-11-10 09:00:00','timezone'=>'Australia/Melbourne','duration_minutes'=>120]);ok($c['staff_ids']===[3,5],'staff normalization');ok($c['window']['end']-$c['window']['start']===7200,'duration');
echo "pass05_command OK\n";
