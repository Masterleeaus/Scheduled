<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingOwnerIdentity,BookingOwnerOperationPolicy,BookingTenantWritePreflight,BookingWriteResponseGuard,BookingRescheduleFuturePolicy,BookingTenantMutationDocumentation,BookingLockKeys,BookingRescheduleLockPlan,BookingMutationActor};
function check(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
function rejects(callable $callback):void {try{$callback();}catch(\InvalidArgumentException|\DomainException|\RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
BookingTenantWritePreflight::assertReady(['booking_writes_enabled'=>true,'booking_schema_verified'=>true,'booking_rate_mapping_verified'=>true,'booking_concurrency_verified'=>true,'verified_pricing_settings'=>[]]);check(true,'preflight');
echo 'pass15 write_preflight_good ok'.PHP_EOL;
