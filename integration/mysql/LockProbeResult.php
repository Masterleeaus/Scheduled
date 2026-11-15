<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class LockProbeResult {
 public static function verified(array $result):bool {
  return ($result['acquired']??null)===true && ($result['released']??null)===true;
 }
}
