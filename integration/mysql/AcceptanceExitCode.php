<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class AcceptanceExitCode {
 public const PASS=0;public const FAIL=1;public const SKIP=77;
 public static function forStatus(bool $driverPresent,?array $decision=null):int {
  if(!$driverPresent)return self::SKIP;
  return ($decision['infrastructure_ready']??false)===true?self::PASS:self::FAIL;
 }
}
