<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Reject failed tenant identity SQL even when PDO is configured in silent mode. */
final class BookingDatabaseLookup {
 public static function current(\PDO $db):string {
  $query=$db->query('SELECT DATABASE()');
  if($query===false)throw new \RuntimeException('Tenant database query failed');
  $value=$query->fetchColumn();
  if(!is_string($value)||$value==='')throw new \DomainException('Database not selected');
  return $value;
 }
}
