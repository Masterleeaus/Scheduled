<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use DateTimeImmutable;
final class BreakWindow {
 public static function forDay(array $rows,DateTimeImmutable $day,string $zone,array $scheduleRows): array {
  $out=[];
  foreach ($rows as $r) {
   $match=false;
   foreach ($scheduleRows as $schedule) {
    if (ScheduleCalendar::matches($schedule,$day) && (int)$schedule['week_id']===(int)$r['week_id'] && (int)$schedule['weekday_id']===(int)$r['weekday_id']) {$match=true;break;}
   }
   if (!$match) continue;
   $a=TimeOfDay::parse((string)$r['break_start']);$b=TimeOfDay::parse((string)$r['break_end']);if ($b<=$a) continue;
   $start=Interval::parse($day->format('Y-m-d').' '.TimeOfDay::format($a),$zone)->getTimestamp();
   $end=Interval::parse($day->format('Y-m-d').' '.TimeOfDay::format($b),$zone)->getTimestamp();
   if ($end>$start) $out[]=[$start,$end];
  }
  return IntervalSet::merge($out);
 }
}
