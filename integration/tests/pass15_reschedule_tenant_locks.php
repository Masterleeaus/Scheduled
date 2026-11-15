<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingOwnerIdentity,BookingOwnerOperationPolicy,BookingTenantWritePreflight,BookingWriteResponseGuard,BookingRescheduleFuturePolicy,BookingTenantMutationDocumentation,BookingLockKeys,BookingRescheduleLockPlan,BookingMutationActor};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $callback):void {try{$callback();}catch(\InvalidArgumentException|\DomainException|\RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$keys=BookingRescheduleLockPlan::keys(7,[2,3],[3,4],'123456789abc');check(count($keys)===4,'wrong lock cardinality');foreach($keys as $key)check(str_starts_with($key,'tb:123456789abc:'),'unqualified lock');
echo 'pass15 reschedule_tenant_locks ok'.PHP_EOL;
