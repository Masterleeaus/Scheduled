<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Fully validate every DB result before making a booking availability decision. */
final class BookingConflictRows {
 public static function occupied(array $rows,int $staff,string $zone,int $from,int $until):array {
  if(count($rows)>5000)throw new \DomainException('Conflict scan truncated');
  BookingConflictRowIdentity::unique($rows);
  $out=[];
  foreach($rows as $row){$value=BookingConflictRowInterval::parse($row,$staff,$zone);if(Interval::overlaps($from,$until,$value['start'],$value['end']))$out[]=['booking_id'=>$value['booking_id'],'start'=>$value['local_start'],'duration_minutes'=>$value['duration_minutes'],'status'=>$value['status']];}
  return $out;
 }
}
