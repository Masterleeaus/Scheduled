<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Explicitly refuse emulated database drivers for production writes. */
final class BookingDriverGuard {
 public static function assertMySql(\PDO $db):void {
  if($db->getAttribute(\PDO::ATTR_DRIVER_NAME)!=='mysql')throw new \DomainException('MySQL required for booking mutations');
  if($db->getAttribute(\PDO::ATTR_EMULATE_PREPARES)===true)throw new \DomainException('Native prepared statements required');
 }
}
