<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** A single fail-closed admission policy used by future public booking handlers. */
final class BookingBookingLifecycleGuard {
 public static function assertReady(array $config,array $evidence):void {
  BookingWriteFeatureGate::requireEnabled($config);
  $readiness=BookingCancellationReadiness::check($evidence);
  if(!$readiness['ready'])throw new \RuntimeException('Booking mutation runtime not verified');
 }
}
