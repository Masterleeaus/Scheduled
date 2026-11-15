<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Fail closed for any busy interval after excluding the current booking. */
final class BookingRescheduleOverlapPolicy {
 public static function assertFree(array $busy,int $bookingId):void {
  if(BookingRescheduleConflictFilter::withoutSelf($busy,$bookingId)!==[])throw new \DomainException('Staff time unavailable');
 }
}
