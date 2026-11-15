<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Staff locks are acquired BEFORE transaction start and released AFTER completion. */
final class CommitCleantoLocks {
 public static function create(\PDO $db):CommitLegacyLockBoundary {
  $lease=new \Titan\BookingSidecar\BookingSharedLockLease($db);
  return new CommitLegacyLockBoundary(
   static fn(\PDO $db,array $selection)=>$lease->acquire($db,$selection),
   static fn(\PDO $db)=>$lease->release()
  );
 }
}
