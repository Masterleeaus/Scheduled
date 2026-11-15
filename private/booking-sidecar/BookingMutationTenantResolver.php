<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Resolves a DB identity for lock plans; refuses request-supplied tenant identifiers. */
final class BookingMutationTenantResolver {
 public static function fromConnection(\PDO $db):string {
  return BookingTenantDatabase::name($db);
 }
 public static function assertNoClientTenant(array $payload):void {
  foreach(['tenant_id','tenant_database','db_name','database'] as $field)if(array_key_exists($field,$payload))throw new \DomainException('Tenant database cannot be chosen by client');
 }
}
