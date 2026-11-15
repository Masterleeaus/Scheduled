<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Required tenant configuration comes from the trusted server-side registry. */
final class CommitCleantoTenantPolicy {
 public static function validate(int $owner,array $config):void {
  if($owner<1||($config['owner_id']??null)!==$owner)throw new \DomainException('Booking owner mismatch');
  foreach(['booking_writes_enabled','booking_schema_verified','booking_rate_mapping_verified','booking_concurrency_verified'] as $field){
   if(($config[$field]??null)!==true)throw new \DomainException('Booking release not verified');
  }
  if(!is_array($config['verified_pricing_settings']??null))throw new \DomainException('Pricing settings unavailable');
 }
}
