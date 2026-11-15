<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicStaffId {
 public static function parse(mixed $input):int {
  if(!is_int($input)&&(!is_string($input)||!preg_match('/^[1-9][0-9]{0,9}$/D',$input)))throw new \InvalidArgumentException('Invalid cleaner');
  $id=filter_var($input,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>2147483647]]);
  if($id===false)throw new \InvalidArgumentException('Invalid cleaner');return (int)$id;
 }
}
