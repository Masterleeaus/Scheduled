<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Durations are elapsed minutes, never local wall-clock increments across DST. */
final class BookingWallClockDuration {
 public static function end(\DateTimeImmutable $start,int $minutes):\DateTimeImmutable {
  if($minutes<15||$minutes>720)throw new \InvalidArgumentException('Invalid duration');
  return $start->setTimestamp($start->getTimestamp()+$minutes*60);
 }
}
