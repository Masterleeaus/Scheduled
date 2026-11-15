<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingIdempotency,BookingLedgerConflictPolicy,BookingLedgerSchemaContract,BookingSchemaMigrationGuard,BookingDatabaseIdentity,BookingEngineRequirements,BookingTestFixtureGuard,BookingWriteSafetyAudit,BookingConflictWindowGuard,BookingSqlDiagnostics};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$f=str_repeat('a',64);$check(BookingIdempotency::decide(['fingerprint'=>$f,'state'=>'completed'],$f)==='replay');
echo "pass12 ledger_complete ok\n";
