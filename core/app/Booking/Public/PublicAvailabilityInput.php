<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicAvailabilityInput {
 public static function parse(array $input):array {
  $allowed=['staff_id','date','duration_minutes','timezone'];
  foreach(array_keys($input) as $key)if(!in_array($key,$allowed,true))throw new \InvalidArgumentException('Unexpected availability field');
  $staff=PublicStaffId::parse($input['staff_id']??null);
  $duration=PublicDuration::parse($input['duration_minutes']??null);
  $zone=PublicTimezone::parse($input['timezone']??'Australia/Melbourne');
  $date=PublicDay::parse($input['date']??null,$zone);
  return ['staff_id'=>$staff,'duration_minutes'=>$duration,'step_minutes'=>15,'date'=>$date,'timezone'=>$zone];
 }
}
