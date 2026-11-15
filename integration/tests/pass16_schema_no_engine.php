<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingTenantScopeGuard,BookingSchemaColumnContract,BookingSchemaIndexContract,BookingSchemaAuditResult,BookingSchemaReleaseGate,BookingReplayPayloadGuard,BookingDatabaseIdentity};
function ok($b):void{if(!$b)throw new RuntimeException('assertion failed');}
function deny(callable $cb):void{try{$cb();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$c=BookingSchemaColumnContract::required();$i=[];foreach(BookingSchemaIndexContract::required() as $t=>$cols)foreach($cols as $col)$i[$t][]=['column'=>$col,'unique'=>true];$r=BookingSchemaAuditResult::evaluate($c,$i,[]);ok(!$r['ready']);
echo 'PASS pass16 schema_no_engine'.PHP_EOL;
