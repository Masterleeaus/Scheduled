<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Explicitly assert known cancellation states, not merely any status change. */
final class LifecycleCancellationStatus {
 public static function verify(array $previous,array $current):void {
  LifecycleCancellationEvidence::verify($previous,$current);
  if(!in_array($current['booking_status'],['CC','CS'],true))throw new \RuntimeException('Booking did not reach a customer/business cancellation status');
 }
}
