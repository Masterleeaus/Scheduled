<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Cleanto appointment persistence does not constitute settled payment. */
final class BookingPaymentBoundary {
 public static function forBooking(array $response):array {
  return BookingWriteResponseGuard::sanitize($response);
 }
}
