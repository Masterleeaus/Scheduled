<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingRescheduleCommand,BookingRescheduleState,BookingRescheduleLockPlan,BookingRescheduleDurationPolicy,BookingRescheduleConflictFilter,BookingRescheduleOverlapPolicy,BookingRescheduleScheduleEvidence,BookingReschedulePreview,BookingRescheduleReadiness,BookingRescheduleSqlContract,BookingRescheduleIdempotency};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$c=['booking_id'=>2,'staff_ids'=>[3],'window'=>['start'=>20,'end'=>30]];$e=['source'=>'server_schedule','verified'=>true,'staff_ids'=>[3],'window_start'=>20,'window_end'=>30];$reject(fn()=>BookingReschedulePreview::assess($c,['status'=>'C'],[],$e));
echo "pass11 preview_missing_staff ok\n";
