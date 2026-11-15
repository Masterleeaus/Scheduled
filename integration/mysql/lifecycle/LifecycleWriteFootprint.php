<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Verify a single create and zero side effects for idempotency and mutation operations. */
final class LifecycleWriteFootprint {
 public static function created(array $before,array $after):void {LifecycleFootprintDelta::assert($before,$after,1,1);}
 public static function unchanged(array $before,array $after):void {LifecycleFootprintDelta::assert($before,$after,0,0);}
}
