<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedRowPolicy {
 public static function normalize(array $row,string $zone):array {
  $id=filter_var($row['id']??null,FILTER_VALIDATE_INT);$duration=filter_var($row['order_duration']??null,FILTER_VALIDATE_INT);
  if($id===false || $id<1 || $duration===false)throw new \DomainException('Invalid persisted appointment identity/duration');
  $staff=PersistedStaffSet::parse((string)($row['staff_ids']??''));
  $range=PersistedInterval::fromLocal((string)($row['booking_date_time']??''),(int)$duration,$zone);
  $status=(string)($row['booking_status']??'');
  return ['id'=>(int)$id,'staff_ids'=>$staff,'interval'=>$range,'status'=>$status,'occupied'=>PersistedStatusPolicy::occupies($status)];
 }
}
