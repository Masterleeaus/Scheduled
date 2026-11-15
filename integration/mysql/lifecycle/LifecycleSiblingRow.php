<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Strict, PII-free snapshot of other bookings sharing the same order. */
final class LifecycleSiblingRow {
 public static function parse(array $r):array {
  $id=filter_var($r['id']??null,FILTER_VALIDATE_INT);
  $order=filter_var($r['order_id']??null,FILTER_VALIDATE_INT);
  if(!$id||!$order||$id<1||$order<1)throw new \RuntimeException('Invalid sibling identity');
  $dt=(string)($r['booking_date_time']??'');
  if(!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D',$dt))throw new \RuntimeException('Invalid sibling time');
  $staff=PersistedStaffSet::parse((string)($r['staff_ids']??''));
  if(!$staff)throw new \RuntimeException('Invalid sibling staff');
  $status=(string)($r['booking_status']??'');
  if(!in_array($status,['A','C','RS','R','CS','CO','CC','MN'],true))throw new \RuntimeException('Invalid sibling status');
  return ['id'=>(int)$id,'order_id'=>(int)$order,'booking_date_time'=>$dt,'staff_ids'=>$staff,'booking_status'=>$status];
 }
}
