<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Confirms immutable booking ID and staff-lock set without issuing a DB mutation. */
final class BookingRescheduleMutationPreflight {
 public static function check(string $tenant,int $bookingId,array $previous,array $next):array {
  $keys=BookingRescheduleLockPlan::keys($bookingId,$previous,$next,$tenant);
  BookingTenantLockPolicy::verify($tenant,$keys);
  return ['booking_id'=>$bookingId,'keys'=>$keys,'read_only'=>true];
 }
}
