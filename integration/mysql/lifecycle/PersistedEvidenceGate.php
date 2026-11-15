<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedEvidenceGate {
 public static function assert(array $evidence,int $winner):void {
  PersistedEvidenceConsistency::assert($evidence,$winner);
 }
}
