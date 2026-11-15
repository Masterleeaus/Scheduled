<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Booking lock and staff locks are acquired together in deterministic order. */
final class BookingRescheduleLockPlan {
 public static function keys(int $bookingId,array $previous,array $next,?string $tenantNamespace=null):array {
  $ids=array_merge(BookingStaff::parse($next),$previous);
  $ids=BookingStaff::parse(array_values(array_unique($ids)));
  $keys=$tenantNamespace===null ? array_merge([BookingLockKeys::forBooking($bookingId)],BookingLockKeys::forStaff($ids)) : array_merge([BookingLockKeys::forTenantBooking($tenantNamespace,$bookingId)],BookingLockKeys::forTenantStaff($tenantNamespace,$ids));
  sort($keys,SORT_STRING);return array_values(array_unique($keys));
 }
}
