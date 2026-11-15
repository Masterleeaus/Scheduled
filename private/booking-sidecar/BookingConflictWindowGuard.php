<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Legacy duration is bounded to 30 days; window scans need a conservative lookback. */
final class BookingConflictWindowGuard {
 public static function lookbackSeconds(int $maxDurationMinutes=43200):int {
  if($maxDurationMinutes<1||$maxDurationMinutes>43200)throw new \InvalidArgumentException('Unsupported Cleanto duration');
  return $maxDurationMinutes*60;
 }
 public static function isOverlap(int $start,int $durationMinutes,int $windowStart,int $windowEnd):bool {
  if($durationMinutes<1||$durationMinutes>43200||$windowEnd<=$windowStart)throw new \InvalidArgumentException('Invalid interval');
  return $start < $windowEnd && ($start+$durationMinutes*60)>$windowStart;
 }
}
