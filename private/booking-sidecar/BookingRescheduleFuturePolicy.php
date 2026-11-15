<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** A reschedule is a new reservation, never a retrospective modification. */
final class BookingRescheduleFuturePolicy {
 public static function assertFuture(array $window,int $now):void {
  $start=$window['start']??null;$end=$window['end']??null;
  if(!is_int($start)||!is_int($end)||$start<=$now||$end<=$start)throw new \DomainException('Reschedule must target a future time');
  if($start>$now+366*86400)throw new \DomainException('Reschedule too far ahead');
 }
}
