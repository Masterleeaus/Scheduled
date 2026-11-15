<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicDuration {
 public static function parse(mixed $input):int {
  if(!is_int($input)&&(!is_string($input)||!preg_match('/^[1-9][0-9]{0,3}$/D',$input)))throw new \InvalidArgumentException('Invalid duration');
  $duration=filter_var($input,FILTER_VALIDATE_INT,['options'=>['min_range'=>15,'max_range'=>720]]);
  if($duration===false || $duration%15!==0)throw new \InvalidArgumentException('Unsupported duration');return (int)$duration;
 }
}
