<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Verify the stored Cleanto order day matches the exact selected local appointment day. */
final class BookingWriteProofOrderDate {
 public static function check($stored,$localStart):void {
  if (!is_string($localStart) || !preg_match('/^\d{4}-\d{2}-\d{2} [0-2]\d:[0-5]\d:[0-5]\d$/D', $localStart)) {
   throw new \DomainException('Invalid local booking start for order-date proof');
  }
  $date=substr($localStart,0,10);
  $parsed=\DateTimeImmutable::createFromFormat('!Y-m-d', $date);
  if ($parsed===false || $parsed->format('Y-m-d')!==$date) {
   throw new \DomainException('Invalid local order date');
  }
  if (!is_string($stored) || $stored!==$date) {
   throw new \DomainException('Persisted Cleanto order date mismatch');
  }
 }
}
