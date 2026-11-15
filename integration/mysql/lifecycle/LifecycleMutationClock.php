<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Cancellation preserves appointment time; reschedule commits requested local time. */
final class LifecycleMutationClock {
 public static function cancellation(array $before,array $after):void {
  if(($before['booking_date_time']??null)!==($after['booking_date_time']??null))throw new \RuntimeException('Cancellation changed appointment time');
 }
 public static function reschedule(array $after,array $move):void {
  if(($after['booking_date_time']??null)!==($move['start_at']??null))throw new \RuntimeException('Reschedule time mismatch');
 }
}
