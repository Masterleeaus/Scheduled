<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Availability evidence must be server generated, never supplied by an anonymous browser. */
final class BookingRescheduleScheduleEvidence {
 public static function verify(array $evidence,array $staffIds,array $window):void {
  $staff=BookingStaff::parse($staffIds);
  if(($evidence['source']??null)!=='server_schedule'||($evidence['verified']??null)!==true)throw new \DomainException('Schedule not verified');
  if(($evidence['staff_ids']??null)!==$staff||($evidence['window_start']??null)!==$window['start']||($evidence['window_end']??null)!==$window['end'])throw new \DomainException('Stale schedule evidence');
 }
}
