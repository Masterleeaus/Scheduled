<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;use DomainException;
/** Rechecks occupancy under the per-staff advisory locks before inserting. */
final class BookingConflictGuard {
 public function __construct(private PDO $db){}
 public function assertFree(array $cmd):void {
  $window=$cmd['window'];BookingConflictWindowSafety::assert($window);
  // Existing reader uses original Cleanto occupancy semantics and order durations.
  foreach($cmd['staff_ids'] as $staff){
   $busy=(new BookingConflictReader($this->db))->busy($staff,$window['local_start'],$window['local_end'],$cmd['timezone']);
   $intervals=BookingBusyWindow::fromRecords($busy,$cmd['timezone']);
   foreach($intervals as $pair){if(Interval::overlaps($window['start'],$window['end'],$pair[0],$pair[1]))throw new DomainException('Staff already booked');}
  }
 }
}
