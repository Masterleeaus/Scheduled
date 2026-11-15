<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingRescheduleCommand,BookingRescheduleState,BookingRescheduleLockPlan,BookingRescheduleDurationPolicy,BookingRescheduleConflictFilter,BookingRescheduleOverlapPolicy,BookingRescheduleScheduleEvidence,BookingReschedulePreview,BookingRescheduleReadiness,BookingRescheduleSqlContract,BookingRescheduleIdempotency};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$s=file_get_contents(__DIR__.'/../../private/booking-sidecar/Dispatcher.php');$check(!str_contains($s,"operation === 'reschedule'"));
echo "pass11 dispatch_fail_closed ok\n";
