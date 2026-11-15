<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Check internal UTC interval representation before applying occupancy comparisons. */
final class BookingConflictWindowSafety {
 public static function assert(array $window):void {
  foreach(['start','end'] as $key)if(!isset($window[$key])||!is_int($window[$key]))throw new \InvalidArgumentException('Invalid conflict window');
  if($window['end']<=$window['start']||$window['end']-$window['start']>43200*60)throw new \InvalidArgumentException('Unbounded conflict interval');
 }
}
