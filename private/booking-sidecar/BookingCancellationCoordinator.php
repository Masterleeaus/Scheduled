<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/**
 * Tenant-scoped cancellation. Lock order: booking lock, then the booking's current staff locks
 * (same per-staff serialization as creation and reschedule), then a DB row lock with requery.
 * Entered only through the owner-gated dispatcher; default-off by BookingWriteFeatureGate.
 */
final class BookingCancellationCoordinator {
 public function __construct(private \PDO $db,private array $config){}
 public function cancel(array $input,array $actor):array {
  BookingWriteFeatureGate::requireEnabled($this->config);
  BookingMutationConnectionGate::assertReady($this->db,$this->config);
  if(($actor['role']??null)==='owner')BookingOwnerIdentity::assertMatches($actor,$this->config);
  $command=BookingCancellationInput::parse($input);$actor=BookingMutationActor::normalize($actor);
  $fingerprint=BookingMutationFingerprint::of('cancel',$actor,$command);
  $locks=new BookingLockManager($this->db);
  $lockDeadlineNs=hrtime(true)+5000000000;
  try {
   $locks->acquire(BookingTenantMutationLocks::cancellation($this->db,$command['booking_id']),5,$lockDeadlineNs);
   // Unlocked snapshot only selects which staff locks to take; the locked requery below is authoritative.
   $snapshot=(new BookingRescheduleRecordReader($this->db))->get($command['booking_id']);
   $locks->acquire(BookingTenantMutationLocks::creation($this->db,$snapshot['staff_ids']),5,$lockDeadlineNs);
   return $this->locked($command,$actor,$fingerprint,$snapshot);
  }finally{$locks->releaseAll();}
 }
 private function locked(array $command,array $actor,string $fingerprint,array $snapshot):array {
  return (new BookingTransaction($this->db))->run(function()use($command,$actor,$fingerprint,$snapshot){
   $ledger=new BookingMutationLedger($this->db);$previous=$ledger->forUpdate($command['idempotency_key']);
   $replayed=BookingMutationReplay::evaluate($previous,$fingerprint);
   if($replayed!==null){if($replayed!==$command['booking_id'])throw new \DomainException('Cancellation replay refers to another booking');if($actor['role']==='customer')(new BookingMutationCustomerScope($this->db))->assertBookingOwner($replayed,$actor['id']);(new BookingMutationStateProof($this->db))->verifyReplay($replayed,'cancel');return ['booking_id'=>$replayed,'replayed'=>true,'status'=>'cancelled'];}
   $record=(new BookingMutationReader($this->db))->locked($command['booking_id']);
   // The staff set and time seen under the staff locks must still be the ones we locked for.
   if($record['staff_ids']!==$snapshot['staff_ids']||$record['start']!==$snapshot['start']||$record['order_id']!==$snapshot['order_id'])throw new \DomainException('Booking changed concurrently');
   if($actor['role']==='customer')(new BookingMutationCustomerScope($this->db))->assertBookingOwner($record['id'],$actor['id']);
   $target=BookingCancellationPolicy::target($record['status'],$actor['role']);
   $ledger->claim($command['idempotency_key'],$fingerprint);
   (new BookingLegacyStatusWriter($this->db))->cancel($record['id'],$record['status'],$target,$command['reason']);
   (new BookingMutationStateProof($this->db))->verifyCancelled($record['id'],$target);
   $ledger->complete($command['idempotency_key'],$record['id']);
   return ['booking_id'=>$record['id'],'replayed'=>false,'status'=>'cancelled'];
  });
 }
}
