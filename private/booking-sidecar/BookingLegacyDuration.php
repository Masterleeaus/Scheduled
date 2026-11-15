<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Parse Cleanto order-duration values without PHP's permissive integer coercion. */
final class BookingLegacyDuration {
 public static function minutes(mixed $raw):int {
  if(is_int($raw))$minutes=$raw;
  elseif(is_string($raw)&&preg_match('/^[1-9][0-9]{0,4}$/D',$raw))$minutes=(int)$raw;
  else throw new \RuntimeException('Invalid persisted booking duration');
  if($minutes<1||$minutes>43200)throw new \RuntimeException('Booking duration outside audited horizon');
  return $minutes;
 }
}
