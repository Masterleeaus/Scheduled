<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Avoid leaking credentials and DSNs in CLI error output. */
final class RedactedFailure {
 public static function message(\Throwable $e):string {
  $msg=$e->getMessage();
  $msg=preg_replace('/(?:password|passwd|pwd)\s*[=:]\s*[^\s;]+/i','password=[REDACTED]',$msg);
  $msg=preg_replace('/mysql:host=[^\s;]+/i','mysql:host=[REDACTED]',$msg);
  return substr((string)$msg,0,300);
 }
}
