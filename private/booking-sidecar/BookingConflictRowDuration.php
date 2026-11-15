<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Cleanto stores booked duration on an order, not on the appointment row. */
final class BookingConflictRowDuration {
 public static function minutes(mixed $raw):int {
  try{$minutes=BookingLegacyDuration::minutes($raw);}catch(\Throwable $e){throw new \DomainException('Unusable persisted order duration',0,$e);}
  BookingConflictHorizon::validateMinutes($minutes);
  return $minutes;
 }
}
