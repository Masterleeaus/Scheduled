<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingLockNamespace,BookingLockKeys,BookingRescheduleLockPlan,BookingTenantLockPolicy,BookingMutationAuthorization,BookingMutationRequestScope,BookingMutationLockInventory,BookingTenantIsolationProbe,BookingLockReleaseReadiness,BookingMutationReleaseGate,BookingRescheduleMutationPreflight,BookingMutationTenantResolver};
$ok=static function(bool $p):void {if(!$p)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $f):void {try{$f();}catch(\Throwable $e){return;}throw new RuntimeException('Accepted invalid input');};
$reject(fn()=>BookingMutationRequestScope::key('cancel','x'));$reject(fn()=>BookingMutationRequestScope::key('delete','abcdefgh'));
echo "pass14 request_bad_id ok\n";
