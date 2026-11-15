<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Fail closed when a mutation lock is unqualified or belongs to another tenant. */
final class BookingTenantLockPolicy {
 public static function verify(string $tenant,array $keys):array {
  if($keys===[])throw new \DomainException('Mutation requires booking locks');
  $plan=BookingLockPlanAudit::inspect($tenant,$keys);
  if($plan['duplicates']!==0)throw new \DomainException('Duplicate lock keys');
  if($plan['keys']!==$keys)throw new \DomainException('Lock order must be canonical');
  return $keys;
 }
}
