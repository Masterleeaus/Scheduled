<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Return the stable order ID captured independently from database. */
final class LifecycleOrderIdentity {
 public static function assert(array $response,array $row):void {
  if(!is_int($response['order_id']??null)||$response['order_id']!==$row['order_id'])throw new \RuntimeException('Created order ID does not match database');
 }
}
