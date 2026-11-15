<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingQuoteEvidence {
 public static function report(array $price,array $input):array {
  if(($price['verified_scope']??'')!=='exact_rule_no_addons')throw new \DomainException('Unverified quote');
  if(!empty($input['addons'])||!empty($input['coupon'])||!empty($input['tax'])||!empty($input['deposit']))throw new \DomainException('Unverified price modifiers');
  return ['amount'=>BookingPriceDecimal::display($price['total_cents']),'price_basis'=>$price['verified_scope'],'booking_writable'=>false];
 }
}
