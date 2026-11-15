<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;
/** Transaction boundary for legacy booking mutations. Never reports a false commit as success. */
final class BookingTransaction {
 public function __construct(private PDO $db){}
 public function run(callable $callback):mixed {
  if($this->db->inTransaction())throw new \LogicException('Nested booking transaction prohibited');
  if($this->db->beginTransaction()!==true)throw new \RuntimeException('Booking transaction could not start');
  try {
   $out=$callback($this->db);
   if(!$this->db->inTransaction())throw new \RuntimeException('Booking writer closed transaction unexpectedly');
   if($this->db->commit()!==true)throw new \RuntimeException('Booking commit failed; outcome uncertain');
   return $out;
  } catch(\Throwable $primary) {
   if($this->db->inTransaction()){
    try {if($this->db->rollBack()!==true)throw new \RuntimeException('Rollback returned false');}
    catch(\Throwable $rollback) {throw new \RuntimeException('Booking rollback failed; outcome uncertain',0,$primary);}
   }
   throw $primary;
  }
 }
}
