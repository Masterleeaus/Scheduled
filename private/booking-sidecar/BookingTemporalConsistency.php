<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Verify persisted seconds and local timestamp refer to same instant before booking commit. */
final class BookingTemporalConsistency {
 public static function assertWindow(array $window,string $zone):void {
  $start=BookingLocalTimeResolution::resolve((string)($window['local_start']??''),$zone);
  $end=(int)($window['end']??0);
  if($start->getTimestamp()!==(int)($window['start']??0)||$end<=$start->getTimestamp())throw new \DomainException('Booking time window mismatch');
  if($end-$start->getTimestamp()>720*60)throw new \DomainException('Booking interval exceeds limit');
  if(isset($window['local_end'])){
   $expected=$start->setTimestamp($end)->format('Y-m-d H:i:s');
   if(!hash_equals($expected,(string)$window['local_end']))throw new \DomainException('Booking end timestamp mismatch');
  }
 }
}
