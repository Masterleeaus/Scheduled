<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Retain the MySQL advisory locks until the owning transaction has ended. */
final class BookingSharedLockLease {
 private BookingLockManager $manager;
 private \PDO $db;
 public function __construct(\PDO $db){$this->db=$db;$this->manager=new BookingLockManager($db);}
 public function acquire(\PDO $db,array $selection):void {
  if($db!==$this->db)throw new \LogicException('Lock connection mismatch');
  if($this->db->inTransaction())throw new \LogicException('Acquire staff locks before booking transaction');
  $this->manager->acquire(BookingSharedStaffLocks::keys($db,$selection));
 }
 public function release():void {
  if($this->db->inTransaction())throw new \LogicException('Release staff locks after booking transaction');
  $this->manager->releaseAll();
 }
 public function held():array{return $this->manager->held();}
}
