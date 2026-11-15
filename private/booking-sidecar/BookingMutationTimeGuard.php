<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Revalidate appointment instant just before an attempted DB write. */
final class BookingMutationTimeGuard {
 public static function assertFuture(array $window,int $clock,int $leadMinutes=0):void {
  if($leadMinutes<0||$leadMinutes>1440)throw new \InvalidArgumentException('Invalid lead time');
  if(($window['start']??0)<=$clock+$leadMinutes*60)throw new \DomainException('Appointment begins too soon or in the past');
 }
}
