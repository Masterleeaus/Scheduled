<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** A checked tenant DB must match server-side tenant registry, never input payload. */
final class BookingTenantScopeGuard {
 public static function assertMatching(string $expected,string $actual):void {
  $expected=BookingDatabaseIdentity::validate($expected);
  $actual=BookingDatabaseIdentity::validate($actual);
  if(!hash_equals($expected,$actual))throw new \DomainException('Connected booking database differs from authorized tenant');
 }
 public static function assertConnection(\PDO $db,array $tenant):void {
  if(!isset($tenant['database'])||!is_string($tenant['database']))throw new \DomainException('Tenant database pin missing');
  $current=BookingDatabaseLookup::current($db);
  if(!is_string($current))throw new \DomainException('Database not selected');
  self::assertMatching($tenant['database'],$current);
 }
}
