<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Re-check observed schema under the caller's transaction, not just tenant flags. */
final class BookingLiveWriteGuard {
 public static function assert(\PDO $db,array $tenant):void {
  BookingDriverGuard::assertMySql($db);
  BookingTenantScopeGuard::assertConnection($db,$tenant);
  if(!$db->inTransaction())throw new \LogicException('Live booking schema check requires transaction');
  $evidence=BookingLiveWriteEvidence::assess((new BookingLiveWriteSnapshot($db))->read());
  if(!$evidence['ready'])throw new \DomainException('Cleanto write schema not ready: '.implode(',',$evidence['errors']));
 }
}
