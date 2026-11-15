<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Check real Cleanto storage bounds immediately before persistence. */
final class BookingLiveWriteCommandPolicy {
 public static function assert(array $command):void {
  BookingLiveWriteCustomer::assert($command['customer']??[]);
  BookingPersonalInfo::parse($command['personal_info']??[]);
  BookingLiveWriteStaff::assert($command['staff_ids']??[]);
  foreach(['service_id','method_id','method_unit_id'] as $field)BookingLiveWriteIds::foreignId($command['service'][$field]??0);
  if(!is_int($command['duration_minutes']??null)||$command['duration_minutes']<15||$command['duration_minutes']>720)throw new \InvalidArgumentException('Invalid duration');
 }
}
