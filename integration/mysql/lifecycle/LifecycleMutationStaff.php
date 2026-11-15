<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Cancellation retains staff assignment; reschedule uses only requested staff. */
final class LifecycleMutationStaff {
 public static function cancellation(array $before,array $after):void {
  if(($before['staff_ids']??null)!==($after['staff_ids']??null))throw new \RuntimeException('Cancellation changed staff assignments');
 }
 public static function reschedule(array $after,array $move):void {
  $staff=PersistedStaffSet::parse(implode(',',(array)($move['staff_ids']??[])));
  if(!$staff||($after['staff_ids']??null)!==$staff)throw new \RuntimeException('Reschedule staff mismatch');
 }
}
