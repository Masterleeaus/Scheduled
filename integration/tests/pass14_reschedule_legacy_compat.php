<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingLockNamespace,BookingLockKeys,BookingRescheduleLockPlan,BookingTenantLockPolicy,BookingMutationAuthorization,BookingMutationRequestScope,BookingMutationLockInventory,BookingTenantIsolationProbe,BookingLockReleaseReadiness,BookingMutationReleaseGate,BookingRescheduleMutationPreflight,BookingMutationTenantResolver};
$ok=static function(bool $p):void {if(!$p)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $f):void {try{$f();}catch(\Throwable $e){return;}throw new RuntimeException('Accepted invalid input');};
$k=BookingRescheduleLockPlan::keys(4,[2],[1]);$ok(in_array('tb:booking:4',$k,true));
echo "pass14 reschedule_legacy_compat ok\n";
