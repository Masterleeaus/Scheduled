<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Normalize a Cleanto record without trusting API response fields. */
final class LifecyclePersistedRow {
 public static function normalize(array $row):array {
  $id=filter_var($row['id']??null,FILTER_VALIDATE_INT);$order=filter_var($row['order_id']??null,FILTER_VALIDATE_INT);
  $service=filter_var($row['service_id']??null,FILTER_VALIDATE_INT);
  if(!$id||$id<1||!$order||$order<1||!$service||$service<1)throw new \RuntimeException('Invalid persisted booking identity');
  if (!is_string($row['staff_ids']??null)) throw new \RuntimeException('Persisted staff IDs must be a database string');
  $ids=PersistedStaffSet::parse($row['staff_ids']);
  if(!$ids)throw new \RuntimeException('Persisted booking lacks staff');
  $status=(string)($row['booking_status']??'');if(!in_array($status,['A','C','RS','R','CS','CO','CC','MN'],true))throw new \RuntimeException('Unknown stored booking status');
  $time=(string)($row['booking_date_time']??'');if(!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D',$time))throw new \RuntimeException('Invalid stored booking time');
  return ['id'=>(int)$id,'order_id'=>(int)$order,'service_id'=>(int)$service,'staff_ids'=>$ids,'booking_date_time'=>$time,'booking_status'=>$status];
 }
}
