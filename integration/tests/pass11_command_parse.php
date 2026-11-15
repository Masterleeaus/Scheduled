<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingRescheduleCommand,BookingRescheduleState,BookingRescheduleLockPlan,BookingRescheduleDurationPolicy,BookingRescheduleConflictFilter,BookingRescheduleOverlapPolicy,BookingRescheduleScheduleEvidence,BookingReschedulePreview,BookingRescheduleReadiness,BookingRescheduleSqlContract,BookingRescheduleIdempotency};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$r=BookingRescheduleCommand::parse(['booking_id'=>2,'duration_minutes'=>60,'start_at'=>'2026-11-16 09:00:00','timezone'=>'Australia/Melbourne','staff_ids'=>[3],'idempotency_key'=>str_repeat('k',24)]);$check($r['booking_id']===2&&$r['staff_ids']===[3]);
echo "pass11 command_parse ok\n";
