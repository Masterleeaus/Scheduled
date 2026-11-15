<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Machine-readable operational limitations for deploy audits. */
final class BookingTenantMutationDocumentation {
 public static function requirements():array {
  return ['mysql_transactions','schema_parity','staff_locks','tenant_isolation','pricing_parity','concurrency_testing','customer_ownership','payment_settlement_separate'];
 }
 public static function releaseable(array $checks):bool {
  foreach(self::requirements() as $requirement)if(($checks[$requirement]??null)!==true)return false;
  return true;
 }
}
