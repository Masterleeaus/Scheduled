<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pricing is read afresh under the caller-owned transaction. */
final class BookingSharedRate {
 public static function authoritative(\PDO $db,array $command,array $settings):string {
  if(!is_array($settings['verified_pricing_settings']??null))throw new \DomainException('Pricing configuration unavailable');
  return (new BookingServiceRateGuard($db,$settings['verified_pricing_settings']))->confirmedRate($command['service']);
 }
}
