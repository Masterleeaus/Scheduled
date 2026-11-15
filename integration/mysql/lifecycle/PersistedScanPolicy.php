<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedScanPolicy {
 public const MAX_ROWS=5000;
 public static function assertBounded(array $rows):void {
  if(count($rows)>self::MAX_ROWS)throw new \RuntimeException('Persisted appointment scan truncated');
 }
}
