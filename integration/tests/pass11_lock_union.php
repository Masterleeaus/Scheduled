<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingRescheduleCommand,BookingRescheduleState,BookingRescheduleLockPlan,BookingRescheduleDurationPolicy,BookingRescheduleConflictFilter,BookingRescheduleOverlapPolicy,BookingRescheduleScheduleEvidence,BookingReschedulePreview,BookingRescheduleReadiness,BookingRescheduleSqlContract,BookingRescheduleIdempotency};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$v=BookingRescheduleLockPlan::keys(2,[2,3],[3,4]);$check(count($v)===4&&count(array_unique($v))===4);
echo "pass11 lock_union ok\n";
