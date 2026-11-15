<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingLockNamespace,BookingLockKeys,BookingRescheduleLockPlan,BookingTenantLockPolicy,BookingMutationAuthorization,BookingMutationRequestScope,BookingMutationLockInventory,BookingTenantIsolationProbe,BookingLockReleaseReadiness,BookingMutationReleaseGate,BookingRescheduleMutationPreflight,BookingMutationTenantResolver};
$ok=static function(bool $p):void {if(!$p)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $f):void {try{$f();}catch(\Throwable $e){return;}throw new RuntimeException('Accepted invalid input');};
$r=BookingMutationReleaseGate::evaluate(['mysql_transaction'=>true]);$ok(!$r['ready']&&!$r['booking_writes_enabled']);
echo "pass14 release_gate ok\n";
