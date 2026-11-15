<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Moving or cancelling an appointment cannot silently mutate order duration. */
final class LifecycleOrderIntegrity {
 public static function verify(array $before,array $after):void {
  if(LifecycleOrderSnapshot::from($before)!==LifecycleOrderSnapshot::from($after))throw new \RuntimeException('Shared order metadata changed');
 }
}
