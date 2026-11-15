<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{AcceptanceReport,LockProbeResult,SchemaFailureReport,SchemaInventory,TransactionalTableAudit,AcceptanceDecision,AcceptanceExitCode,RedactedFailure,BookingWriteReleaseBlocker,MysqlTestConfig};
function ok(bool $condition):void{if(!$condition)throw new RuntimeException('assertion failed');}
function reject(callable $callback):void{try{$callback();}catch(DomainException|RuntimeException|InvalidArgumentException $e){return;}throw new RuntimeException('expected rejection');}
$a=SchemaFailureReport::fromInventories([],[]);ok(!$a['valid'] && count($a['missing'])>0);
echo 'PASS pass19_schema_missing_fail'.PHP_EOL;
