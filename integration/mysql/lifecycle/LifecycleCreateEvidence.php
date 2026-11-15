<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Require a separate database read after creation has committed. */
final class LifecycleCreateEvidence {
 public static function verify(array $row,int $bookingId,array $fixture):void {
  $identity=ConcurrencyFixtureIdentity::from($fixture);
  if($row['id']!==$bookingId||$row['service_id']!==$identity['service_id']||$row['staff_ids']!==$identity['staff_ids']||$row['booking_date_time']!==$identity['start_at'])throw new \RuntimeException('Creation did not persist expected booking');
  if($row['booking_status']!=='A')throw new \RuntimeException('Creation did not persist active status');
 }
}
