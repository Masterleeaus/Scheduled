<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Obtain independent baseline from read-only connection before each mutation. */
final class LifecycleAcceptanceBaseline {
 public static function capture(\PDO $reader,int $id):array {
  $booking=LifecycleFullBookingReader::read($reader,$id);
  return ['booking'=>$booking,'order'=>LifecycleClientOrderReader::read($reader,$booking['order_id']),
   'siblings'=>LifecycleSiblingReader::read($reader,$booking['order_id'],$id)];
 }
}
