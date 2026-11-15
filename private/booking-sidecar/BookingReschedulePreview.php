<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Read-only preflight: callers must fetch busy intervals and schedule evidence on the server. */
final class BookingReschedulePreview {
 public static function assess(array $command,array $current,array $busyByStaff,array $evidence):array {
  BookingRescheduleState::assertEligible($current['status']);
  BookingRescheduleScheduleEvidence::verify($evidence,$command['staff_ids'],$command['window']);
  foreach($command['staff_ids'] as $staff) {
   if(!array_key_exists($staff,$busyByStaff)||!is_array($busyByStaff[$staff]))throw new \DomainException('Incomplete staff conflicts');
   BookingRescheduleOverlapPolicy::assertFree($busyByStaff[$staff],$command['booking_id']);
  }
  return ['booking_id'=>$command['booking_id'],'preview_only'=>true,'available'=>true,'window'=>$command['window']];
 }
}
