<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Standardize SKIP versus FAIL so CI never counts skipped MySQL checks as a pass. */
final class BookingTestExitPolicy {
 public static function status(int $code):string {
  return match($code){0=>'passed',77=>'skipped',default=>'failed'};
 }
 public static function requireExecutable(string $driver):void {
  if($driver!=='mysql')throw new \RuntimeException('pdo_mysql unavailable for integration verification');
 }
}
