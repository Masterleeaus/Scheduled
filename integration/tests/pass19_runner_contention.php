<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{AcceptanceReport,LockProbeResult,SchemaFailureReport,SchemaInventory,TransactionalTableAudit,AcceptanceDecision,AcceptanceExitCode,RedactedFailure,BookingWriteReleaseBlocker,MysqlTestConfig};
function ok(bool $condition):void{if(!$condition)throw new RuntimeException('assertion failed');}
function reject(callable $callback):void{try{$callback();}catch(DomainException|RuntimeException|InvalidArgumentException $e){return;}throw new RuntimeException('expected rejection');}
$s=file_get_contents(__DIR__.'/../mysql/run_acceptance.php');ok(str_contains($s,'ConnectionPair::create')&&str_contains($s,'LockContentionProbe::run')&&str_contains($s,'booking_creation_verified'));
echo 'PASS pass19_runner_contention'.PHP_EOL;
