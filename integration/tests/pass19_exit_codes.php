<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{AcceptanceReport,LockProbeResult,SchemaFailureReport,SchemaInventory,TransactionalTableAudit,AcceptanceDecision,AcceptanceExitCode,RedactedFailure,BookingWriteReleaseBlocker,MysqlTestConfig};
function ok(bool $condition):void{if(!$condition)throw new RuntimeException('assertion failed');}
function reject(callable $callback):void{try{$callback();}catch(DomainException|RuntimeException|InvalidArgumentException $e){return;}throw new RuntimeException('expected rejection');}
ok(AcceptanceExitCode::forStatus(false)===77 && AcceptanceExitCode::forStatus(true,['infrastructure_ready'=>true])===0 && AcceptanceExitCode::forStatus(true,[])===1);
echo 'PASS pass19_exit_codes'.PHP_EOL;
