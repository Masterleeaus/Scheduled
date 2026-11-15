<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{AcceptanceReport,LockProbeResult,SchemaFailureReport,SchemaInventory,TransactionalTableAudit,AcceptanceDecision,AcceptanceExitCode,RedactedFailure,BookingWriteReleaseBlocker,MysqlTestConfig};
function ok(bool $condition):void{if(!$condition)throw new RuntimeException('assertion failed');}
function reject(callable $callback):void{try{$callback();}catch(DomainException|RuntimeException|InvalidArgumentException $e){return;}throw new RuntimeException('expected rejection');}
BookingWriteReleaseBlocker::requireLifecycleEvidence(array_fill_keys(['real_insert','concurrent_overlap','idempotency','cancel','reschedule','pricing_parity','tenant_isolation'],true));ok(true);
echo 'PASS pass19_release_full_evidence'.PHP_EOL;
