<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewSettings {
 public static function assertVerified(array $s):void {
  $findings=BookingPriceAudit::findings($s);
  if($findings) throw new \DomainException('Price configuration needs parity review: '.implode(', ', $findings));
 }
}
