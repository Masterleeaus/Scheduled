<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use DateTimeImmutable;
/** Cleanto weekday_id follows Sunday=1 through Saturday=7. Monthly week_id is 1..5. */
final class ScheduleCalendar {
 public static function keys(DateTimeImmutable $date): array {
  $weekday=(int)$date->format('w')+1;
  $monthWeek=(int)ceil((int)$date->format('j')/7);
  return ['weekday_id'=>$weekday,'week_id'=>$monthWeek];
 }
 public static function matches(array $row, DateTimeImmutable $date): bool {
  $keys=self::keys($date);
  $kind=strtolower((string)($row['provider_schedule_type']??''));
  if (!in_array($kind,['weekly','monthly'],true)) return false;
  return (int)$row['weekday_id']===$keys['weekday_id'] && (int)$row['week_id']===($kind==='weekly'?1:$keys['week_id']);
 }
}
