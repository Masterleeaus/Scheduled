<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyOutcomeEvidence {
 public static function merge(array $outcome,array $database):array {
  if(($outcome['passed']??null)!==true||($database['persisted']??null)!==true||($outcome['accepted_booking_id']??null)!==($database['booking_id']??null))throw new \RuntimeException('Concurrency evidence mismatch');
  return $outcome+['database_readback'=>$database];
 }
}
