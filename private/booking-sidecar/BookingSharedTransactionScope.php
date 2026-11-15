<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Verify the caller owns the transaction; never begin or commit here. */
final class BookingSharedTransactionScope {
 public static function assert(\PDO $db,array $tenant):void {
  if(!$db->inTransaction())throw new \LogicException('Shared transaction required');
  BookingDriverGuard::assertMySql($db);
  BookingTenantScopeGuard::assertConnection($db,$tenant);
 }
}
