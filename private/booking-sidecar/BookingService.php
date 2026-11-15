<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Service identity and quantity normalization, preserving integer price quantities. */
final class BookingService {
 public static function parse(array $input):array {
  return BookingPriceInput::parse($input);
 }
}
