<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingLockNamespace,BookingLockKeys,BookingRescheduleLockPlan,BookingTenantLockPolicy,BookingMutationAuthorization,BookingMutationRequestScope,BookingMutationLockInventory,BookingTenantIsolationProbe,BookingLockReleaseReadiness,BookingMutationReleaseGate,BookingRescheduleMutationPreflight,BookingMutationTenantResolver};
$ok=static function(bool $p):void {if(!$p)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $f):void {try{$f();}catch(\Throwable $e){return;}throw new RuntimeException('Accepted invalid input');};
$n=BookingLockNamespace::fromDatabase('test_titan_booking_one');$reject(fn()=>BookingTenantLockPolicy::verify($n,[]));
echo "pass14 lock_empty ok\n";
