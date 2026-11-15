<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Tenant-scoped cancellation with booking lock, DB row lock, ownership and idempotency. Disabled at dispatcher. */
final class BookingCancellationCoordinator {
 public function __construct(private \PDO $db,private array $config){}
 public function cancel(array $input,array $actor):array {
  BookingWriteFeatureGate::requireEnabled($this->config);
  BookingMutationConnectionGate::assertReady($this->db,$this->config);
  if(($actor['role']??null)==='owner')BookingOwnerIdentity::assertMatches($actor,$this->config);
  $command=BookingCancellationInput::parse($input);$actor=BookingMutationActor::normalize($actor);
  $fingerprint=BookingMutationFingerprint::of('cancel',$actor,$command);
  $locks=new BookingLockManager($this->db);$locks->acquire(BookingTenantMutationLocks::cancellation($this->db,$command['booking_id']));
  try {return (new BookingTransaction($this->db))->run(function()use($command,$actor,$fingerprint){
   $ledger=new BookingMutationLedger($this->db);$previous=$ledger->forUpdate($command['idempotency_key']);
   $replayed=BookingMutationReplay::evaluate($previous,$fingerprint);
   if($replayed!==null){if($replayed!==$command['booking_id'])throw new \DomainException('Cancellation replay refers to another booking');if($actor['role']==='customer')(new BookingMutationCustomerScope($this->db))->assertBookingOwner($replayed,$actor['id']);(new BookingMutationStateProof($this->db))->verifyReplay($replayed,'cancel');return ['booking_id'=>$replayed,'replayed'=>true,'status'=>'cancelled'];}
   $record=(new BookingMutationReader($this->db))->locked($command['booking_id']);
   if($actor['role']==='customer')(new BookingMutationCustomerScope($this->db))->assertBookingOwner($record['id'],$actor['id']);
   $target=BookingCancellationPolicy::target($record['status'],$actor['role']);
   $ledger->claim($command['idempotency_key'],$fingerprint);
   (new BookingLegacyStatusWriter($this->db))->cancel($record['id'],$record['status'],$target,$command['reason']);
   (new BookingMutationStateProof($this->db))->verifyCancelled($record['id'],$target);
   $ledger->complete($command['idempotency_key'],$record['id']);
   return ['booking_id'=>$record['id'],'replayed'=>false,'status'=>'cancelled'];
  });}finally{$locks->releaseAll();}
 }
}
