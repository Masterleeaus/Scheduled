<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingIdempotency,BookingLedgerConflictPolicy,BookingLedgerSchemaContract,BookingSchemaMigrationGuard,BookingDatabaseIdentity,BookingEngineRequirements,BookingTestFixtureGuard,BookingWriteSafetyAudit,BookingConflictWindowGuard,BookingSqlDiagnostics};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$check(strlen(BookingDatabaseIdentity::safeReference('test_titan_booking_demo'))===12);$reject(fn()=>BookingDatabaseIdentity::validate('x;drop'));
echo "pass12 database_identity ok\n";
