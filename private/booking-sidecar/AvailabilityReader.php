<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;
/** Original Cleanto schema adapter: all queries use server-selected tenant PDO. */
final class AvailabilityReader {
 public function __construct(private PDO $db) {}
 public function slots(array $input): array {
  $p=AvailabilityPayload::parse($input);$day=Interval::parse($p['date'].' 00:00:00',$p['timezone']);
  $next=$day->modify('+1 day');$start=$day->format('Y-m-d H:i:s');$until=$next->format('Y-m-d H:i:s');
  $reader=new ScheduleReader($this->db);
  $busy=(new BookingConflictReader($this->db))->busy($p['staff_id'],$start,$until,$p['timezone']);
  return ['slots'=>AvailabilityEngine::calculate($p,$reader->shifts($p['staff_id']),$reader->breaks($p['staff_id']),$reader->timeOff($p['staff_id'],$start,$until),$reader->daysOff($p['staff_id'],$p['date']),$busy)];
 }
}
