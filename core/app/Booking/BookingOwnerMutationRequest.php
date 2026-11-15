<?php
namespace App\Booking;
/** Strict payload boundaries for owner-initiated cancellation and rescheduling. */
final class BookingOwnerMutationRequest {
 public static function cancel(array $data):array {
  $id=filter_var($data['booking_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);if($id===false)throw new \InvalidArgumentException('Invalid booking');
  return ['booking_id'=>(int)$id,'idempotency_key'=>$data['idempotency_key']??null,'reason'=>$data['reason']??''];
 }
 public static function reschedule(array $data):array {
  $id=filter_var($data['booking_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
  $duration=filter_var($data['duration_minutes']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>15,'max_range'=>720]]);
  if($id===false||$duration===false||!is_array($data['staff_ids']??null))throw new \InvalidArgumentException('Invalid reschedule');
  return ['booking_id'=>(int)$id,'duration_minutes'=>(int)$duration,'staff_ids'=>array_map('intval',$data['staff_ids']),'start_at'=>$data['start_at']??null,'timezone'=>$data['timezone']??null,'idempotency_key'=>$data['idempotency_key']??null,'reason'=>$data['reason']??''];
 }
}
