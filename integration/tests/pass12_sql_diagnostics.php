<?php
declare(strict_types=1);
require __DIR__.'/../../private/booking-sidecar/bootstrap.php';
use Titan\BookingSidecar\{BookingIdempotency,BookingLedgerConflictPolicy,BookingLedgerSchemaContract,BookingSchemaMigrationGuard,BookingDatabaseIdentity,BookingEngineRequirements,BookingTestFixtureGuard,BookingWriteSafetyAudit,BookingConflictWindowGuard,BookingSqlDiagnostics};
$check=static function(bool $ok):void {if(!$ok)throw new RuntimeException('Assertion failed');};
$reject=static function(callable $fn):void {try{$fn();}catch(InvalidArgumentException|DomainException|RuntimeException $e){return;}throw new RuntimeException('Unexpected acceptance');};
$s=BookingSqlDiagnostics::summary(['ok'=>false,'missing'=>['x.y']],['database_ref'=>'abcdef123456','transactional_engine'=>true],['ready'=>false,'missing'=>['pdo_mysql']]);$check(!$s['schema_ok']&&$s['database_ref']==='abcdef123456');
echo "pass12 sql_diagnostics ok\n";
