<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** One single-visit appointment per transaction. Reuses original Cleanto tables. */
final class BookingWriteCoordinator {
 public function __construct(private \PDO $db,private array $config){}
 public function create(array $input):array {
  BookingSubmissionSafety::assertSafe($input);
  BookingWriteFeatureGate::requireEnabled($this->config);
  BookingMutationConnectionGate::assertReady($this->db,$this->config);
  BookingTenantWritePreflight::assertReady($this->config);
  $cmd=BookingCommand::create($input);
  BookingCreationPolicy::assertScope($input,$cmd);
  BookingMutationTimeGuard::assertFuture($cmd['window'],time());
  $fingerprint=BookingKey::fingerprint($cmd);
  $locks=new BookingLockManager($this->db);
  try {
   $locks->acquire(BookingTenantMutationLocks::creation($this->db,$cmd['staff_ids']));
   return (new BookingTransaction($this->db))->run(function()use($cmd,$fingerprint){
    $ledger=new BookingRequestLedger($this->db);
    $previous=$ledger->getForUpdate($cmd['idempotency_key']);
    $decision=BookingIdempotency::decide($previous,$fingerprint);
    if($decision==='replay'){
     $replayed=BookingReplayPayloadGuard::bookingId($previous);
     $replayOrder=BookingCreationReplayProof::verifiedOrderId($this->db,$replayed,$cmd);
     return ['booking_id'=>$replayed,'order_id'=>$replayOrder,'replayed'=>true,'status'=>'active'];
    }
    if($decision==='pending')throw new \DomainException('Booking request pending');
    // The initial future check happened before potentially contested staff locks.
    // Recheck inside the transaction, keeping legitimate idempotent replays allowed.
    BookingMutationTimeGuard::assertFuture($cmd['window'],time());
    $ledger->claim($cmd['idempotency_key'],$fingerprint);
    // Revalidate pricing and availability AFTER taking the staff locks.
    $price=(new BookingServiceRateGuard($this->db,$this->config['verified_pricing_settings']??[]))->confirmedRate($cmd['service']);
    (new BookingFinalAvailability($this->db))->assertAvailable($cmd);
    (new BookingConflictGuard($this->db))->assertFree($cmd);
    // Availability queries can themselves be slow; never persist an elapsed slot.
    BookingMutationTimeGuard::assertFuture($cmd['window'],time());
    $order=(new BookingOrderSequence($this->db))->next();
    $client=(new BookingLegacyClientWriter($this->db))->insert($order,$cmd['customer'],$cmd['duration_minutes'],$cmd['personal_info']);
    $booking=(new BookingLegacyAppointmentWriter($this->db))->insert($order,$client,$cmd,$price);
    BookingAtomicPersistProof::verify($this->db,$booking,$cmd,$price);
    $ledger->complete($cmd['idempotency_key'],$booking);
    return ['booking_id'=>$booking,'order_id'=>$order,'status'=>'active','replayed'=>false,'payment_status'=>'unpaid'];
   });
  }finally{$locks->releaseAll();}
 }
}
