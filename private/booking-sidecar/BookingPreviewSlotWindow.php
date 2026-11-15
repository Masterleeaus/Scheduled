<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewSlotWindow {
 public static function filter(array $slots,string $date,string $zone,int $minutes):array {
  $day=Interval::parse($date.' 00:00:00',$zone);
  $next=$day->modify('+1 day')->getTimestamp();
  $out=[];
  foreach($slots as $t){
   if(!is_int($t)||$t<$day->getTimestamp()||$t+$minutes*60>$next)throw new \DomainException('Slot outside requested local date');
   $out[]=$t;
  }
  return $out;
 }
}
