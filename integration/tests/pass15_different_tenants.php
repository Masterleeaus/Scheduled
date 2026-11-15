<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingOwnerIdentity,BookingOwnerOperationPolicy,BookingTenantWritePreflight,BookingWriteResponseGuard,BookingRescheduleFuturePolicy,BookingTenantMutationDocumentation,BookingLockKeys,BookingRescheduleLockPlan,BookingMutationActor};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $callback):void {try{$callback();}catch(\InvalidArgumentException|\DomainException|\RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$a=BookingLockKeys::forTenantStaff('123456789abc',[7]);$b=BookingLockKeys::forTenantStaff('abcdef123456',[7]);check($a!==$b,'cross tenant lock collision');
echo 'pass15 different_tenants ok'.PHP_EOL;
