<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Required server-configured capabilities, never a customer-controlled boolean. */
final class BookingTenantWritePreflight {
 public static function assertReady(array $settings):void {
  foreach(['booking_writes_enabled','booking_schema_verified','booking_rate_mapping_verified','booking_concurrency_verified'] as $key){
   if(($settings[$key]??null)!==true)throw new \DomainException('Booking write preflight failed: '.$key);
  }
  if(!is_array($settings['verified_pricing_settings']??null))throw new \DomainException('Verified pricing settings required');
 }
}
