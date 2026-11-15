<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Enforce expected Cleanto transitions in persisted records. */
final class LifecycleMutationStatus {
 public static function rescheduled(array $before,array $after):void {
  if(!in_array($before['booking_status']??null,['A','RS'],true)||($after['booking_status']??null)!=='RS')throw new \RuntimeException('Invalid persisted rescheduling transition');
 }
 public static function cancelled(array $before,array $after):void {
  if(!in_array($before['booking_status']??null,['A','RS'],true)||!in_array($after['booking_status']??null,['CC','CS'],true))throw new \RuntimeException('Invalid persisted cancellation transition');
 }
}
