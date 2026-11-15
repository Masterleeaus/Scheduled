<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Do not accept a cancellation API response without a persisted state transition. */
final class LifecycleCancellationEvidence {
 public static function verify(array $previous,array $current):void {
  if($current['id']!==$previous['id']||$current['order_id']!==$previous['order_id']||$current['service_id']!==$previous['service_id'])throw new \RuntimeException('Cancellation changed booking identity');
  if(in_array($current['booking_status'],['A','C','RS'],true)||$current['booking_status']===$previous['booking_status'])throw new \RuntimeException('Cancellation not persisted');
 }
}
