<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Immutable order association evidence without personal data or payment assumptions. */
final class LifecycleOrderSnapshot {
 public static function from(array $order):array {
  $id=filter_var($order['order_id']??null,FILTER_VALIDATE_INT);
  $duration=filter_var($order['order_duration']??null,FILTER_VALIDATE_INT);
  if(!$id||$id<1||$duration===false||$duration<1)throw new \RuntimeException('Invalid order snapshot');
  return ['order_id'=>(int)$id,'order_duration'=>(int)$duration];
 }
}
