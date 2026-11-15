<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingTenantScopeGuard,BookingSchemaColumnContract,BookingSchemaIndexContract,BookingSchemaAuditResult,BookingSchemaReleaseGate,BookingReplayPayloadGuard,BookingDatabaseIdentity};
function ok($b):void{if(!$b)throw new RuntimeException('assertion failed');}
function deny(callable $cb):void{try{$cb();}catch(DomainException|InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection');}
$i=[];foreach(BookingSchemaIndexContract::required() as $t=>$cols)foreach($cols as $col)$i[$t][]=['column'=>$col,'unique'=>false];ok(count(BookingSchemaIndexContract::absent($i))===3);
echo 'PASS pass16 indexes_non_unique'.PHP_EOL;
