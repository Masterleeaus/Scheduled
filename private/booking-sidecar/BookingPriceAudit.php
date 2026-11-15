<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingPriceAudit {
 public static function findings(array $settings):array {
  $out=[];
  foreach(['tax_enabled','discount_enabled','coupon_enabled','addons_enabled','travel_fee_enabled','deposit_enabled'] as $key)if(($settings[$key]??null)!==false)$out[]='Unverified setting: '.$key;
  return $out;
 }
}
