<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class PersistedEvidenceConsistency {
 public static function assert(array $evidence,int $winner):void {
  if(($evidence['persisted']??null)!==true)throw new \RuntimeException('Persisted winner was not independently verified');
  if(($evidence['overlap_scan_passed']??null)!==true)throw new \RuntimeException('Overlap scan was not independently verified');
  $ids=$evidence['overlapping_booking_ids']??null;
  if(!is_array($ids)||array_values($ids)!==[$winner]||($evidence['overlapping_booking_count']??null)!==1)
   throw new \RuntimeException('Overlapping appointments do not identify one winner');
  $scanned=$evidence['scanned_rows']??null;
  if(!is_int($scanned)||$scanned<1||$scanned>PersistedScanPolicy::MAX_ROWS||$scanned<1)
   throw new \RuntimeException('Invalid persisted scan count');
 }
}
