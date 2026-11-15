<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyResultGuard {
 public static function assertDistinct(array $left,array $right):void {
  if(($left['outcome']??null)==='accepted'&&($right['outcome']??null)==='accepted'&&($left['booking_id']??null)===$right['booking_id'])throw new \RuntimeException('Both workers claimed same booking');
 }
}
