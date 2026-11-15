<?php
declare(strict_types=1);
require __DIR__.'/../mysql/bootstrap.php';
use Titan\BookingIntegration\{AcceptanceReport,LockProbeResult,SchemaFailureReport,SchemaInventory,TransactionalTableAudit,AcceptanceDecision,AcceptanceExitCode,RedactedFailure,BookingWriteReleaseBlocker,MysqlTestConfig};
function ok(bool $condition):void{if(!$condition)throw new RuntimeException('assertion failed');}
function reject(callable $callback):void{try{$callback();}catch(DomainException|RuntimeException|InvalidArgumentException $e){return;}throw new RuntimeException('expected rejection');}
$a=AcceptanceDecision::evaluate(['valid'=>true],['isolated'=>false,'released'=>true],['rollback'=>true],['safe'=>true]);ok(!$a['infrastructure_ready'] && in_array('lock_contention',$a['failures'],true));
echo 'PASS pass19_decision_bad_lock'.PHP_EOL;
