<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingTenantScopeGuard,BookingSchemaColumnContract,BookingSchemaIndexContract,BookingSchemaAuditResult,BookingSchemaReleaseGate,BookingReplayPayloadGuard,BookingDatabaseIdentity};
function ok($b):void{if(!$b)throw new RuntimeException('assertion failed');}
function deny(callable $cb):void{try{$cb();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$c=['booking_writes_enabled'=>true,'booking_schema_verified'=>true,'booking_rate_mapping_verified'=>true,'booking_concurrency_verified'=>true];BookingSchemaReleaseGate::assertReady($c,['ready'=>true,'errors'=>[]]);ok(true);
echo 'PASS pass16 release_clean'.PHP_EOL;
