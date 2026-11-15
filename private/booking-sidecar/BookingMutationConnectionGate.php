<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Run before executing ANY booking mutation, fail closed for missing tenant pin. */
final class BookingMutationConnectionGate {
 public static function assertReady(\PDO $db,array $tenant):void {
  BookingDriverGuard::assertMySql($db);
  BookingTenantScopeGuard::assertConnection($db,$tenant);
  if($db->inTransaction())throw new \LogicException('Booking operation cannot start in an ambient transaction');
 }
}
