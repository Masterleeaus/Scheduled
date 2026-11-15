<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Read authenticated SQL connection database identity; caller must not provide tenant name. */
final class BookingTenantDatabase {
 public static function name(\PDO $pdo):string {
  $db=$pdo->query('SELECT DATABASE()')->fetchColumn();
  if(!is_string($db)||$db==='')throw new \RuntimeException('Tenant database is not selected');
  return BookingLockNamespace::fromDatabase($db);
 }
}
