<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingOwnerIdentity,BookingOwnerOperationPolicy,BookingTenantWritePreflight,BookingWriteResponseGuard,BookingRescheduleFuturePolicy,BookingTenantMutationDocumentation,BookingLockKeys,BookingRescheduleLockPlan,BookingMutationActor};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $callback):void {try{$callback();}catch(\InvalidArgumentException|\DomainException|\RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$now=1700000000;BookingRescheduleFuturePolicy::assertFuture(['start'=>$now+3600,'end'=>$now+7200],$now);rejects(fn()=>BookingRescheduleFuturePolicy::assertFuture(['start'=>$now-1,'end'=>$now+500],$now));
echo 'pass15 reschedule_future ok'.PHP_EOL;
