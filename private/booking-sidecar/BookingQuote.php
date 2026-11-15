<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Payment authorisation is external; this validates a server-calculated non-negative total. */
final class BookingQuote {
 public static function total(int $baseCents,int $extrasCents,int $discountCents,int $taxCents):int {
  foreach([$baseCents,$extrasCents,$discountCents,$taxCents] as $x)if($x<0||$x>999999999)throw new InvalidArgumentException('Invalid quote component');
  $subtotal=$baseCents+$extrasCents;if($discountCents>$subtotal)throw new InvalidArgumentException('Discount exceeds price');
  return $subtotal-$discountCents+$taxCents;
 }
}
