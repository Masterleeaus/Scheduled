<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Persisted customer, service, pricing method and monetary rate are immutable during mutation. */
final class LifecycleImmutableIdentity {
 public static function verify(array $old,array $new):void {
  foreach(['id','order_id','client_id','service_id','method_id','method_unit_id','method_unit_qty','rate'] as $key)
   if(!array_key_exists($key,$old)||!array_key_exists($key,$new)||$old[$key]!==$new[$key])throw new \RuntimeException('Booking identity/economics changed: '.$key);
 }
}
