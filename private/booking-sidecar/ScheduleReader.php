<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;
/** Preserves original Cleanto tables; never writes legacy schedule records. */
final class ScheduleReader {
 public function __construct(private PDO $db) {}
 public function shifts(int $staff): array {
  $s=$this->db->prepare('SELECT id,provider_id,week_id,weekday_id,day_start_time,day_end_time,off_day,provider_schedule_type FROM ct_week_days_available WHERE provider_id=? ORDER BY week_id,weekday_id,id');
  if($s===false||$s->execute([$staff])!==true)throw new \RuntimeException('Staff schedule query failed');$rows=$s->fetchAll(PDO::FETCH_ASSOC);if(!is_array($rows))throw new \RuntimeException('Staff schedule unavailable');return $rows;
 }
 public function breaks(int $staff): array {
  $s=$this->db->prepare('SELECT provider_id,week_id,weekday_id,break_start,break_end FROM ct_schedule_breaks WHERE provider_id=? ORDER BY week_id,weekday_id,break_start');
  if($s===false||$s->execute([$staff])!==true)throw new \RuntimeException('Staff schedule query failed');$rows=$s->fetchAll(PDO::FETCH_ASSOC);if(!is_array($rows))throw new \RuntimeException('Staff schedule unavailable');return $rows;
 }
 public function timeOff(int $staff,string $from,string $until): array {
  $s=$this->db->prepare('SELECT start_date_time,end_date_time FROM ct_schedule_offtimes WHERE provider_id=? AND start_date_time < ? AND end_date_time > ? ORDER BY start_date_time');
  if($s===false||$s->execute([$staff,$until,$from])!==true)throw new \RuntimeException('Time-off query failed');$rows=$s->fetchAll(PDO::FETCH_ASSOC);if(!is_array($rows))throw new \RuntimeException('Time-off unavailable');return $rows;
 }
 public function daysOff(int $staff,string $date): array {
  $s=$this->db->prepare('SELECT off_date,status FROM ct_off_days WHERE user_id=? AND off_date=?');
  if($s===false||$s->execute([$staff,$date])!==true)throw new \RuntimeException('Days-off query failed');$rows=$s->fetchAll(PDO::FETCH_ASSOC);if(!is_array($rows))throw new \RuntimeException('Days-off unavailable');return $rows;
 }
}
