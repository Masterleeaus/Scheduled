<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedInterval {
 public static function fromLocal(string $local,int $minutes,string $zone):array {
  if($minutes<1 || $minutes>43200)throw new \DomainException('Unsupported legacy booking duration');
  $start=PersistedLocalClock::instant($local,$zone);
  return [$start,$start+$minutes*60];
 }
 public static function overlap(array $left,array $right):bool {return $left[0]<$right[1] && $right[0]<$left[1];}
}
