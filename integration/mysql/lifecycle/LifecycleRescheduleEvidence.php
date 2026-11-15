<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Verify stored timestamp and assignment after reschedule commit. */
final class LifecycleRescheduleEvidence {
 public static function verify(array $old,array $new,int $id,array $move):void {
  if($old['id']!==$id||$new['id']!==$id||$old['order_id']!==$new['order_id']||$old['service_id']!==$new['service_id'])throw new \RuntimeException('Reschedule changed booking identity');
  $staff=PersistedStaffSet::parse(implode(',',(array)($move['staff_ids']??[])));
  if($new['booking_date_time']!==($move['start_at']??null)||$new['staff_ids']!==$staff)throw new \RuntimeException('Reschedule was not committed');
  if(!in_array($new['booking_status'],['RS'],true))throw new \RuntimeException('Reschedule persisted ineligible status');
 }
}
