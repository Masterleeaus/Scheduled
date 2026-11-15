<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewRateAssumptions {
 public static function settings(array $s):array {
  // Unknown settings must not be silently treated as disabled.
  $out=[];foreach(['tax_enabled','discount_enabled','coupon_enabled','addons_enabled','travel_fee_enabled','deposit_enabled'] as $k){
   $out[$k]=$s[$k]??null;
  }return $out;
 }
}
