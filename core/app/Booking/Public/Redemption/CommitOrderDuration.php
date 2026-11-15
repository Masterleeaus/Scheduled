<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Pass 38: persisted booking/order integrity. */
final class CommitOrderDuration {
 public static function minutes(mixed $value):int {
  if(!(is_int($value)||(is_string($value)&&preg_match('/^[1-9][0-9]*$/D',$value))))throw new \RuntimeException('Invalid stored duration');
  $v=(int)$value;if($v<15||$v>720)throw new \RuntimeException('Invalid stored duration');return $v;
 }
}
