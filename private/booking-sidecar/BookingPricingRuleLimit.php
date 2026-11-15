<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** LIMIT 501 is a truncation sentinel; never quote from a partial set. */
final class BookingPricingRuleLimit {
 public static function complete(array $rows):array {
  if(count($rows)>500)throw new \DomainException('Pricing rule scan truncated');
  return $rows;
 }
}
