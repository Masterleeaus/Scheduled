<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyFailureCategory {
 public static function isConflict(string $reason):bool {
  return (bool)preg_match('/\b(conflict|unavailable|already booked|overlap)\b/i',$reason) && !preg_match('/\b(sql|connection|timeout|permission|fatal|authentication|schema)\b/i',$reason);
 }
}
