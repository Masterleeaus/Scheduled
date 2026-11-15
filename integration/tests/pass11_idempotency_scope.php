<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingRescheduleCommand,BookingRescheduleState,BookingRescheduleLockPlan,BookingRescheduleDurationPolicy,BookingRescheduleConflictFilter,BookingRescheduleOverlapPolicy,BookingRescheduleScheduleEvidence,BookingReschedulePreview,BookingRescheduleReadiness,BookingRescheduleSqlContract,BookingRescheduleIdempotency};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$k=BookingRescheduleIdempotency::key(str_repeat('k',24));$check(strlen($k)<96&&str_starts_with($k,'reschedule_'));
echo "pass11 idempotency_scope ok\n";
