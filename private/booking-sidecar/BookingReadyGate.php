<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingReadyGate {
 public static function check(array $proof):void {
  foreach(['mysql_integration','concurrent_reservations','duration_overlap','staff_schedule','pricing_parity','tenant_authorization','payment_parity'] as $key)if(($proof[$key]??false)!==true)throw new \DomainException('Missing production proof: '.$key);
 }
}
