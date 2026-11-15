<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Strict in-transaction proof after Cleanto cancellation and rescheduling writes. */
final class BookingMutationStateProof {
 public function __construct(private \PDO $db){}
 public function verifyCancelled(int $bookingId,string $target):void {
  $record=$this->locked($bookingId);
  if(!in_array($target,['CC','CS'],true)||$record['status']!==$target)throw new \DomainException('Stored cancellation status mismatch');
 }
 public function verifyRescheduled(int $bookingId,string $expectedStart,array $expectedStaff):void {
  $record=$this->locked($bookingId);
  if($record['status']!=='RS')throw new \DomainException('Stored reschedule status mismatch');
  if($record['start']!==$expectedStart)throw new \DomainException('Stored reschedule time mismatch');
  if($record['staff_ids']!==BookingStaff::parse($expectedStaff))throw new \DomainException('Stored reschedule staff mismatch');
 }
 public function verifyReplay(int $bookingId,string $kind,?array $command=null):void {
  $record=$this->locked($bookingId);
  if($kind==='cancel'){
   if(!in_array($record['status'],['CC','CS'],true))throw new \DomainException('Cancelled booking replay no longer matches stored status');
  }elseif($kind==='reschedule'){
   if(!is_array($command))throw new \LogicException('Reschedule replay command missing');
   if($record['status']!=='RS'||$record['start']!==$command['window']['local_start']||$record['staff_ids']!==BookingStaff::parse($command['staff_ids']))throw new \DomainException('Stored reschedule time or staff mismatch');
  }else throw new \LogicException('Unknown mutation replay operation');
 }
 private function locked(int $id):array {
  if(!$this->db->inTransaction())throw new \LogicException('Mutation proof requires active transaction');
  return (new BookingMutationReader($this->db))->locked(BookingPositiveId::parse($id));
 }
}
