<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingTenantScopeGuard,BookingSchemaColumnContract,BookingSchemaIndexContract,BookingSchemaAuditResult,BookingSchemaReleaseGate,BookingReplayPayloadGuard,BookingDatabaseIdentity};
function ok($b):void{if(!$b)throw new RuntimeException('assertion failed');}
function deny(callable $cb):void{try{$cb();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$a=BookingSchemaColumnContract::missing([]);ok(in_array('ct_bookings.id',$a,true)&&count($a)>20);
echo 'PASS pass16 missing_columns'.PHP_EOL;
