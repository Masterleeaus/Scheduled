<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Strict half-open service appointment window; reuses Cleanto interval parsing. */
final class BookingTimeWindow {
 public static function parse(string $localStart,int $duration,string $zone):array {
  if($duration<15||$duration>720)throw new InvalidArgumentException('Unsupported appointment duration');
  $start=BookingLocalTimeResolution::resolve($localStart,BookingTimeZonePolicy::validate($zone));$end=BookingWallClockDuration::end($start,$duration);
  if($end->getTimestamp()-$start->getTimestamp()!==$duration*60)throw new InvalidArgumentException('Invalid appointment duration');
  return ['start'=>$start->getTimestamp(),'end'=>$end->getTimestamp(),'local_start'=>$localStart,'local_end'=>$end->format('Y-m-d H:i:s')];
 }
 public static function overlaps(array $one,array $two):bool {return Interval::overlaps($one['start'],$one['end'],$two['start'],$two['end']);}
}
