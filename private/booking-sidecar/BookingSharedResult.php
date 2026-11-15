<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** A writer result is an ID only; the redemption coordinator owns confirmation. */
final class BookingSharedResult {
 public static function bookingId(int $id):int {
  if($id<1)throw new \DomainException('Appointment was not saved');
  return $id;
 }
}
