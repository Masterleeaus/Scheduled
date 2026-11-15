<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingTenantScopeGuard,BookingSchemaColumnContract,BookingSchemaIndexContract,BookingSchemaAuditResult,BookingSchemaReleaseGate,BookingReplayPayloadGuard,BookingDatabaseIdentity};
function ok($b):void{if(!$b)throw new RuntimeException('assertion failed');}
function deny(callable $cb):void{try{$cb();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$c=BookingSchemaColumnContract::required();$r=BookingSchemaAuditResult::evaluate($c,[],['ct_bookings'=>'MyISAM']);ok(in_array('requires_innodb:ct_bookings',$r['errors'],true));
echo 'PASS pass16 schema_bad_engine'.PHP_EOL;
