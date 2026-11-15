<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** A legacy wall-clock time must resolve to one and only one instant. */
final class BookingConflictRowTime {
 public static function instant(mixed $raw,string $zone):int {
  if(!is_string($raw)||!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D',$raw))throw new \DomainException('Malformed persisted booking time');
  return BookingLocalTimeResolution::resolve($raw,$zone)->getTimestamp();
 }
}
