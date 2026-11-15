<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class TimeOffWindow {
 public static function fromRows(array $rows,string $zone): array {
  $out=[];foreach ($rows as $r) {
   $a=Interval::parse((string)$r['start_date_time'],$zone)->getTimestamp();
   $b=Interval::parse((string)$r['end_date_time'],$zone)->getTimestamp();
   if ($b<=$a) throw new \RuntimeException('Invalid legacy time off');
   $out[]=[$a,$b];
  }
  return IntervalSet::merge($out);
 }
}
