<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Holds tenant-specific staff locks until after commit (not merely until INSERT). */
final class BookingAtomicLockLease {
 private BookingLockManager $locks;
 public function __construct(private \PDO $db){$this->locks=new BookingLockManager($db);}
 public function acquire(array $selected):void {
  if($this->db->inTransaction())throw new \RuntimeException('Acquire advisory locks before transaction');
  $ids=BookingStaff::parse($selected);
  $this->locks->acquire(BookingTenantMutationLocks::creation($this->db,$ids));
 }
 public function release():void {
  if($this->db->inTransaction())throw new \RuntimeException('Release advisory locks after commit/rollback');
  $this->locks->releaseAll();
 }
}
