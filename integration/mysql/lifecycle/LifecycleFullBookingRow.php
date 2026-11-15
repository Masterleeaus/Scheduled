<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Validate Cleanto record shape before accepting persistence evidence. */
final class LifecycleFullBookingRow {
 public static function parse(array $r):array {
  $out=[];
  foreach(['id','order_id','client_id','service_id','method_id','method_unit_id','method_unit_qty'] as $k){
   $out[$k]=LifecycleLegacyId::parse($r[$k]??null,$k);
  }
  $out['rate']=LifecyclePersistedRate::parse($r['method_unit_qty_rate']??null);
  $out['staff_ids']=PersistedStaffSet::parse((string)($r['staff_ids']??''));
  if(!$out['staff_ids'])throw new \RuntimeException('Missing persisted staff');
  $out['booking_date_time']=LifecyclePersistedTimestamp::parse($r['booking_date_time']??null);
  $out['booking_status']=(string)($r['booking_status']??'');
  if(!in_array($out['booking_status'],['A','C','RS','R','CS','CO','CC','MN'],true))throw new \RuntimeException('Unknown booking status');
  return $out;
 }
}
