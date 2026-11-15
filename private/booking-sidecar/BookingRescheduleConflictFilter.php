<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Existing booking must be excluded when checking the new interval. */
final class BookingRescheduleConflictFilter {
 public static function withoutSelf(array $busy,int $bookingId):array {
  if($bookingId<1)throw new \InvalidArgumentException('Invalid booking');
  return array_values(array_filter($busy,static function($row)use($bookingId){
   if(!is_array($row)||!isset($row['booking_id']))throw new \DomainException('Malformed conflict result');
   return (int)$row['booking_id']!==$bookingId;
  }));
 }
}
