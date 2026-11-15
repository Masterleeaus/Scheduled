<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Explicitly bound max historical Cleanto order duration for overlap lookbacks. */
final class BookingConflictHorizon {
 public const MAX_LEGACY_MINUTES=43200;
 public static function lowerBound(\DateTimeImmutable $start):string {
  return $start->setTimestamp($start->getTimestamp()-self::MAX_LEGACY_MINUTES*60)->format('Y-m-d H:i:s');
 }
 public static function validateMinutes(int $minutes):void {
  if($minutes<1||$minutes>self::MAX_LEGACY_MINUTES)throw new \DomainException('Legacy duration outside audited limit');
 }
}
