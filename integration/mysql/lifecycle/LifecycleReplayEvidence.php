<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Idempotent replay must not mutate the already committed appointment. */
final class LifecycleReplayEvidence {
 public static function verify(array $before,array $after):void {
  if($before!==$after)throw new \RuntimeException('Idempotent replay modified committed booking');
 }
}
