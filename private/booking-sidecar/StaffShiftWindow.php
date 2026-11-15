<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use DateTimeImmutable;
/** Extract local wall-clock working windows, rejecting invalid and overnight ranges (fail closed). */
final class StaffShiftWindow {
 public static function forDay(array $rows,DateTimeImmutable $day,string $zone): array {
  $out=[];
  foreach ($rows as $r) {
   if (!ScheduleCalendar::matches($r,$day) || ($r['off_day']??'Y')!=='N') continue;
   $start=TimeOfDay::parse((string)$r['day_start_time']);$end=TimeOfDay::parse((string)$r['day_end_time']);
   if ($end<=$start) continue;
   $a=Interval::parse($day->format('Y-m-d').' '.TimeOfDay::format($start),$zone)->getTimestamp();
   $b=Interval::parse($day->format('Y-m-d').' '.TimeOfDay::format($end),$zone)->getTimestamp();
   if ($b>$a && $b-$a<=86400) $out[]=[$a,$b];
  }
  return IntervalSet::merge($out);
 }
}
