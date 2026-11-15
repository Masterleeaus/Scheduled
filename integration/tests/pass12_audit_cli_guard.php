<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingIdempotency,BookingLedgerConflictPolicy,BookingLedgerSchemaContract,BookingSchemaMigrationGuard,BookingDatabaseIdentity,BookingEngineRequirements,BookingTestFixtureGuard,BookingWriteSafetyAudit,BookingConflictWindowGuard,BookingSqlDiagnostics};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$p=dirname(__DIR__).'/booking_database_audit.php';$q=escapeshellarg(PHP_BINARY).' '.escapeshellarg($p).' 2>&1';exec($q,$out,$status);if(!in_array('mysql',PDO::getAvailableDrivers(),true))$check($status===3);else $check($status!==0);
echo "pass12 audit_cli_guard ok\n";
