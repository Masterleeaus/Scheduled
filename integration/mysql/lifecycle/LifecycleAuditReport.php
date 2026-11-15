<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** PII-free mutation evidence overview; does not claim an unrun acceptance passed. */
final class LifecycleAuditReport {
 public static function successful(array $before,array $after,array $siblings):array {
  LifecycleImmutableIdentity::verify($before,$after);
  return ['booking_id'=>$after['id'],'order_id'=>$after['order_id'],'sibling_count'=>count($siblings),
   'independent_readback'=>true,'payment_settlement_verified'=>false];
 }
}
