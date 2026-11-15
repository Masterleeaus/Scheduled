<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Check a completed stage using the independent committed database reader. */
final class LifecycleFootprintEvidence {
 public static function create(\PDO $reader,array $before):array {
  $after=LifecycleFootprintSnapshot::read($reader);
  LifecycleWriteFootprint::created($before,$after);
  return $after;
 }
 public static function unchanged(\PDO $reader,array $before):array {
  $after=LifecycleFootprintSnapshot::read($reader);
  LifecycleWriteFootprint::unchanged($before,$after);
  return $after;
 }
}
