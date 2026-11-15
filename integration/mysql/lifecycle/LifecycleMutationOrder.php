<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Assert the sequential acceptance lifecycle has all required stages. */
final class LifecycleMutationOrder {
 public static function assert(array $flags):void {
  foreach(['real_insert','idempotency','sequential_overlap','reschedule','cancel'] as $k)if(($flags[$k]??false)!==true)throw new \RuntimeException('Unverified booking lifecycle stage: '.$k);
 }
}
