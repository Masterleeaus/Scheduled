<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Max legacy duration 30 elapsed days, plus 48-hour cushion for civil-time shifts. */
final class PersistedWindowBounds {
 public const LOOKBACK_SECONDS=43200*60+48*3600;
 public static function forWindow(array $window):array {
  if(!isset($window['zone'],$window['start_at'],$window['interval'][0],$window['interval'][1]))throw new \DomainException('Incomplete persisted fixture window');
  $start=PersistedLocalClock::instant($window['start_at'],$window['zone']);
  if($window['interval'][0]!==$start||!is_int($window['interval'][1])||$window['interval'][1]<=$start)throw new \DomainException('Inconsistent persisted fixture interval');
  $tz=new \DateTimeZone($window['zone']);
  return [(new \DateTimeImmutable('@'.($start-self::LOOKBACK_SECONDS)))->setTimezone($tz)->format('Y-m-d H:i:s'),
   (new \DateTimeImmutable('@'.$window['interval'][1]))->setTimezone($tz)->format('Y-m-d H:i:s')];
 }
}
