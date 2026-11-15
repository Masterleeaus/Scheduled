<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitBookingStatusPolicy {
 public static function active(mixed $status):bool {
  return is_string($status)&&in_array($status,['A','C','CO','RS'],true);
 }
 public static function requireActive(mixed $status):void {
  if(!self::active($status))throw new \RuntimeException('Booking is no longer active');
 }
}
