<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingOwnerIdentity,BookingOwnerOperationPolicy,BookingTenantWritePreflight,BookingWriteResponseGuard,BookingRescheduleFuturePolicy,BookingTenantMutationDocumentation,BookingLockKeys,BookingRescheduleLockPlan,BookingMutationActor};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $callback):void {try{$callback();}catch(\InvalidArgumentException|\DomainException|\RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$keys=BookingTenantMutationDocumentation::requirements();check(count($keys)===8,'requirements missing');check(!BookingTenantMutationDocumentation::releaseable(array_fill_keys($keys,false)),'unsafe release');check(BookingTenantMutationDocumentation::releaseable(array_fill_keys($keys,true)),'verified release');
echo 'pass15 release_check ok'.PHP_EOL;
