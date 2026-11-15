<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Explicit bounded repeat interval; future reservations must be individually checked. */
final class BookingRecurrenceRule {
 public static function parse(array $input):array {
  $type=$input['frequency']??null;$count=$input['count']??null;
  if(!in_array($type,['weekly','fortnightly','monthly'],true)||!is_int($count)||$count<1||$count>52)throw new InvalidArgumentException('Invalid recurrence');
  return ['frequency'=>$type,'count'=>$count];
 }
}
