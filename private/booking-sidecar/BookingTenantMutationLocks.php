<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Tenant-qualified locks for mutations; namespace is always derived from an authenticated PDO connection. */
final class BookingTenantMutationLocks {
 public static function creation(\PDO $db,array $staffIds):array {
  if(!$staffIds)throw new \InvalidArgumentException('Staff locking requires at least one cleaner');
  return BookingLockKeys::forTenantStaff(BookingTenantDatabase::name($db),$staffIds);
 }
 public static function staffUnion(\PDO $db,array $old,array $next):array {
  return self::creation($db,array_merge($old,$next));
 }
 public static function cancellation(\PDO $db,int $bookingId):array {
  return [BookingLockKeys::forTenantBooking(BookingTenantDatabase::name($db),$bookingId)];
 }
 public static function rescheduling(\PDO $db,int $bookingId,array $previous,array $next):array {
  return BookingRescheduleLockPlan::keys($bookingId,$previous,$next,BookingTenantDatabase::name($db));
 }
}
