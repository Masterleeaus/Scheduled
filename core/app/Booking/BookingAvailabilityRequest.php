<?php
namespace App\Booking;
use InvalidArgumentException;
/** Internal Laravel request contract; sidecar revalidates the same fields. */
final class BookingAvailabilityRequest {
 public static function normalize(array $value): array {
  $id=filter_var($value['staff_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
  $duration=filter_var($value['duration_minutes']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>15,'max_range'=>720]]);
  $step=filter_var($value['step_minutes']??15,FILTER_VALIDATE_INT,['options'=>['min_range'=>5,'max_range'=>120]]);
  if ($id===false || $duration===false || $step===false) throw new InvalidArgumentException('Invalid availability request');
  $date=$value['date']??null;$zone=$value['timezone']??'UTC';
  if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D',$date) || !is_string($zone) || !in_array($zone,\DateTimeZone::listIdentifiers(),true)) throw new InvalidArgumentException('Invalid day or timezone');
  $dateValue=\DateTimeImmutable::createFromFormat('!Y-m-d',$date,new \DateTimeZone($zone));
  if (!$dateValue || $dateValue->format('Y-m-d')!==$date) throw new InvalidArgumentException('Invalid date');
  return ['staff_id'=>(int)$id,'duration_minutes'=>(int)$duration,'step_minutes'=>(int)$step,'date'=>$date,'timezone'=>$zone];
 }
}
