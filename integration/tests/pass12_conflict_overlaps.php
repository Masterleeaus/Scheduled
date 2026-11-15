<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingIdempotency,BookingLedgerConflictPolicy,BookingLedgerSchemaContract,BookingSchemaMigrationGuard,BookingDatabaseIdentity,BookingEngineRequirements,BookingTestFixtureGuard,BookingWriteSafetyAudit,BookingConflictWindowGuard,BookingSqlDiagnostics};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$check(BookingConflictWindowGuard::isOverlap(1000,60,1200,2000));$check(!BookingConflictWindowGuard::isOverlap(1000,60,4600,4700));
echo "pass12 conflict_overlaps ok\n";
