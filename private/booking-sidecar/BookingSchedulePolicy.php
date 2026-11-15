<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** A booking must fit wholly inside at least one available window for every allocated staff member. */
final class BookingSchedulePolicy {
 public static function permits(array $window,array $staffFree):bool {
  if(!$staffFree)return false;
  foreach($staffFree as $windows){$valid=false;foreach($windows as [$start,$end])if($start<=$window['start']&&$window['end']<=$end){$valid=true;break;}if(!$valid)return false;}
  return true;
 }
}
