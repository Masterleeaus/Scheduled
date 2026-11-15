<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Query independently and verify all original Cleanto record associations after commit. */
final class LifecycleReadbackAudit {
 public static function verify(\PDO $reader,int $id,array $fixture):array {
  $booking=LifecycleFullBookingReader::read($reader,$id);
  $order=LifecycleClientOrderReader::read($reader,$booking['order_id']);
  return LifecycleFullEvidence::verify($booking,$order,$fixture);
 }
}
