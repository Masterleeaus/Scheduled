<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingIdempotency,BookingLedgerConflictPolicy,BookingLedgerSchemaContract,BookingSchemaMigrationGuard,BookingDatabaseIdentity,BookingEngineRequirements,BookingTestFixtureGuard,BookingWriteSafetyAudit,BookingConflictWindowGuard,BookingSqlDiagnostics};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$r=BookingWriteSafetyAudit::evaluate(array_fill_keys(['tenant_scope','ownership_authorization','schema_verified','pricing_parity','staff_availability','overlap_recheck','parallel_reservation_test','rollback_test','payment_reconciliation','idempotency_replay'],true));$check(!$r['allow_write']&&$r['evidence_complete']);
echo "pass12 write_fail_closed ok\n";
