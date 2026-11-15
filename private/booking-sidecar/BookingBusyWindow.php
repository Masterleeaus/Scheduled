<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Convert validated Cleanto busy rows to occupied elapsed-time intervals. */
final class BookingBusyWindow {
 public static function fromRecords(array $rows,string $zone):array {
  $blocked=[];
  foreach($rows as $row){
   $duration=BookingConflictRowDuration::minutes($row['duration_minutes']??null);
   $start=BookingConflictRowTime::instant($row['start']??null,$zone);
   $blocked[]=[$start,$start+$duration*60];
  }
  return IntervalSet::merge($blocked);
 }
}
