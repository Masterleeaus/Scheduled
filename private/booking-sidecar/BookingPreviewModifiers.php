<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewModifiers {
 public static function assertNone(array $data): void {
  foreach(['addons','coupon','tax','deposit','discount','travel_fee','tip','gift_card'] as $k) {
   if(array_key_exists($k,$data) && $data[$k]!==null && $data[$k]!==[] && $data[$k]!==false && $data[$k]!=='' && $data[$k]!==0) throw new \DomainException('Unsupported price modifier: '.$k);
  }
 }
}
