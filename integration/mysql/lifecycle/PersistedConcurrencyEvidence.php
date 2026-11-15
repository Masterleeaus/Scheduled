<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Independent readback plus complete overlapping-booking count on a fresh DB connection. */
final class PersistedConcurrencyEvidence {
 public static function verify(\PDO $db,int $winner,array $fixture):array {
  $window=PersistedFixtureWindow::from($fixture);
  $readback=ConcurrencyDatabaseEvidence::verify($db,$winner,$fixture);
  $rows=PersistedOverlapReader::read($db,$window);$matches=PersistedOverlapCounter::count($rows,$window);
  PersistedSingleWinner::assert($matches,$winner);
  $evidence=$readback+['overlap_scan_passed'=>true,'overlapping_booking_ids'=>$matches,'overlapping_booking_count'=>count($matches),'scanned_rows'=>count($rows)];
  PersistedEvidenceConsistency::assert($evidence,$winner);
  return $evidence;
 }
}
