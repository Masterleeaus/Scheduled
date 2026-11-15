<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Explicit end-to-end preconditions for safe mutation rollout. */
final class BookingMutationReleaseGate {
 public static function evaluate(array $tests):array {
  $required=['mysql_transaction','tenant_isolation','staff_availability','overlap_concurrency','pricing_parity','customer_ownership','idempotency_replay','rollback','notification_delivery'];
  $missing=[];foreach($required as $key)if(($tests[$key]??false)!==true)$missing[]=$key;
  return ['ready'=>$missing===[],'missing'=>$missing,'booking_writes_enabled'=>false];
 }
}
