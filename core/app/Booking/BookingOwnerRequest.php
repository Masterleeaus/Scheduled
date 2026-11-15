<?php
namespace App\Booking;
/** Server validation before calling the private Cleanto gateway. */
final class BookingOwnerRequest {
 public static function create(array $input):array {
  $required=['idempotency_key','staff_ids','service','customer','start_at','timezone','duration_minutes'];
  foreach($required as $k)if(!array_key_exists($k,$input))throw new \InvalidArgumentException('Missing '.$k);
  if(!is_array($input['service'])||!is_array($input['customer'])||!is_array($input['staff_ids']))throw new \InvalidArgumentException('Invalid booking data');
  $service=$input['service'];foreach(['service_id','method_id','method_unit_id','quantity'] as $key){$n=filter_var($service[$key]??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);if($n===false)throw new \InvalidArgumentException('Invalid service');$service[$key]=(int)$n;}
  $staff=array_map('intval',$input['staff_ids']);if(!$staff||count($staff)>20||min($staff)<1)throw new \InvalidArgumentException('Invalid staff');
  $duration=filter_var($input['duration_minutes'],FILTER_VALIDATE_INT,['options'=>['min_range'=>15,'max_range'=>720]]);if($duration===false)throw new \InvalidArgumentException('Invalid duration');
  return ['idempotency_key'=>$input['idempotency_key'],'staff_ids'=>array_values(array_unique($staff)),'service'=>$service,'customer'=>$input['customer'],'start_at'=>$input['start_at'],'timezone'=>$input['timezone'],'duration_minutes'=>(int)$duration];
 }
}
