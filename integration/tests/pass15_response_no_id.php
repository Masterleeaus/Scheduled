<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingOwnerIdentity,BookingOwnerOperationPolicy,BookingTenantWritePreflight,BookingWriteResponseGuard,BookingRescheduleFuturePolicy,BookingTenantMutationDocumentation,BookingLockKeys,BookingRescheduleLockPlan,BookingMutationActor};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $callback):void {try{$callback();}catch(\InvalidArgumentException|\DomainException|\RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
rejects(fn()=>BookingWriteResponseGuard::sanitize(['booking_id'=>'10']));
echo 'pass15 response_no_id ok'.PHP_EOL;
