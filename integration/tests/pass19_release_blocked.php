<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{AcceptanceReport,LockProbeResult,SchemaFailureReport,SchemaInventory,TransactionalTableAudit,AcceptanceDecision,AcceptanceExitCode,RedactedFailure,BookingWriteReleaseBlocker,MysqlTestConfig};
function ok(bool $condition):void{if(!$condition)throw new RuntimeException('assertion failed');}
function reject(callable $callback):void{try{$callback();}catch(DomainException|RuntimeException|InvalidArgumentException $e){return;}throw new RuntimeException('expected rejection');}
reject(fn()=>BookingWriteReleaseBlocker::requireLifecycleEvidence(['real_insert'=>true]));
echo 'PASS pass19_release_blocked'.PHP_EOL;
