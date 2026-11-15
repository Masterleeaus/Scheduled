<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Converts the selected UI catalogue fields to Cleanto's original command shape. */
final class CommitLegacySelection {
 public static function map(array $raw,array $customer,string $key):array {
  $s=CommitSelection::parse($raw);
  foreach(['service_id','method_id','unit_id','staff_id','quantity','duration_minutes'] as $field){
   if(!is_int($s[$field])||$s[$field]<1)throw new \InvalidArgumentException('Invalid selected '.$field);
  }
  if($s['duration_minutes']<15||$s['duration_minutes']>720)throw new \InvalidArgumentException('Invalid duration');
  if(!is_string($s['date_time'])||!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/D',$s['date_time']))throw new \InvalidArgumentException('Invalid appointment time');
  if(!is_string($s['timezone'])||!in_array($s['timezone'],\DateTimeZone::listIdentifiers(),true))throw new \InvalidArgumentException('Invalid timezone');
  return ['idempotency_key'=>$key,'staff_ids'=>[$s['staff_id']],'service'=>['service_id'=>$s['service_id'],'method_id'=>$s['method_id'],'method_unit_id'=>$s['unit_id'],'quantity'=>$s['quantity']], 'customer'=>$customer,'start_at'=>$s['date_time'],'duration_minutes'=>$s['duration_minutes'],'timezone'=>$s['timezone']];
 }
}
