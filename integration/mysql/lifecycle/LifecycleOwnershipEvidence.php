<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Require both sides of the Cleanto appointment-order customer relationship. */
final class LifecycleOwnershipEvidence {
 public static function verify(array $booking,array $order):void {
  if($booking['order_id']!==$order['order_id']||$booking['client_id']!==$order['id'])throw new \RuntimeException('Stored order/client linkage mismatch');
 }
}
