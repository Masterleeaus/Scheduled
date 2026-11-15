<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Independent evidence must be collected only outside transactions. */
final class LifecycleFootprintBoundary {
 public static function assertCommitted(\PDO $reader):void {
  if($reader->inTransaction())throw new \LogicException('Footprint evidence must be committed');
 }
}
