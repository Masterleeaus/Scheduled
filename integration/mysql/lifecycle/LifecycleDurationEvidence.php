<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Verify persisted order duration agrees with the submitted visit duration. */
final class LifecycleDurationEvidence {
 public static function verify(array $order,array $create):void {
  $minutes=filter_var($create['duration_minutes']??null,FILTER_VALIDATE_INT);
  if($minutes===false||$minutes<15||$minutes>720||$order['order_duration']!==(int)$minutes)throw new \RuntimeException('Stored appointment duration mismatch');
 }
}
