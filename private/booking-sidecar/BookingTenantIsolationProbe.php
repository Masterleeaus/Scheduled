<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pure policy probe verifies distinct database namespaces never share named lock keys. */
final class BookingTenantIsolationProbe {
 public static function assertDistinct(string $firstDatabase,string $secondDatabase,int $bookingId):bool {
  $first=BookingLockNamespace::fromDatabase($firstDatabase);$second=BookingLockNamespace::fromDatabase($secondDatabase);
  if($first===$second)throw new \DomainException('Tenant database namespaces collide');
  return BookingLockKeys::forTenantBooking($first,$bookingId)!==BookingLockKeys::forTenantBooking($second,$bookingId);
 }
}
