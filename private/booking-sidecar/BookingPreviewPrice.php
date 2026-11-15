<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewPrice {
 public static function quote(array $request,array $snapshot,array $rules,array $modifiers=[],array $settings=[]):array {
  BookingPreviewModifiers::assertNone($modifiers);
  BookingPreviewSettings::assertVerified($settings);
  $price=BookingPriceAuthority::verify($request['service'],$snapshot,$rules);
  return ['currency_amount'=>BookingPriceDecimal::display($price['total_cents']),'amount_cents'=>$price['total_cents'],'basis'=>$price['verified_scope'],'payable'=>false];
 }
}
