<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Staff locks cover the complete booking transaction; fail closed if a callback leaks it. */
final class BookingMutationLockBoundary {
 public function __construct(private \PDO $db){}
 public function run(array $keys,callable $action):mixed {
  if($this->db->inTransaction())throw new \LogicException('Acquire booking locks before transaction');
  $tenant=BookingTenantDatabase::name($this->db);
  BookingTenantLockPolicy::verify($tenant,$keys);
  $lock=new BookingLockManager($this->db);
  try {
   $lock->acquire($keys);
   $result=$action();
   if($this->db->inTransaction())throw new \LogicException('Booking callback left transaction open');
   return $result;
  } finally {
   if(!$this->db->inTransaction())$lock->releaseAll();
   // If a callback leaves a transaction active, preserve locks instead of exposing an uncommitted reservation.
  }
 }
}
