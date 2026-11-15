<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Separate destructive lifecycle and competing concurrency experiments. */
final class AcceptanceWindowIsolation {
 public static function assert(array $lifecycle,array $concurrency):void {
  $a=$lifecycle['create'];$b=$concurrency['create'];
  foreach([$a,$b] as $item){
   if(!isset($item['start_at'],$item['duration_minutes'],$item['timezone']) || !is_string($item['start_at']) || !is_int($item['duration_minutes']) || $item['duration_minutes']<1 || $item['duration_minutes']>1440)throw new \DomainException('Fixture window incomplete');
  }
  if($a['timezone']!==$b['timezone'])throw new \DomainException('Fixture timezones must match for comparison');
  $zone=new \DateTimeZone($a['timezone']);
  $windows=[];
  foreach([$a,$b] as $item){
   $date=\DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',$item['start_at'],$zone);
   if(!$date || $date->format('Y-m-d H:i:s')!==$item['start_at'])throw new \DomainException('Invalid fixture date');
   $windows[]=[(int)$date->format('U'),(int)$date->format('U')+60*$item['duration_minutes']];
  }
  if($windows[0][0]<$windows[1][1] && $windows[1][0]<$windows[0][1])throw new \DomainException('Lifecycle and concurrency booking windows overlap');
 }
}
