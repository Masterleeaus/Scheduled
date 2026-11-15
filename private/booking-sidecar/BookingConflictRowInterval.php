<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Normalize original Cleanto booking/order join to a validated occupied interval. */
final class BookingConflictRowInterval {
 public static function parse(array $row,int $staff,string $zone):array {
  $id=BookingConflictRowIdentity::parse($row);
  BookingConflictRowStatus::active($row['booking_status']??null);
  BookingConflictRowStaff::assertContains($row['staff_ids']??null,$staff);
  $start=BookingConflictRowTime::instant($row['booking_date_time']??null,$zone);
  $duration=BookingConflictRowDuration::minutes($row['order_duration']??null);
  return ['booking_id'=>$id,'start'=>$start,'end'=>$start+$duration*60,'duration_minutes'=>$duration,'status'=>$row['booking_status'],'local_start'=>$row['booking_date_time']];
 }
}
