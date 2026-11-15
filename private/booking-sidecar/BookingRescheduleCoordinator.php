<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Booking reschedule using existing Cleanto record writers and staff-scoped locks. */
final class BookingRescheduleCoordinator {
 public function __construct(private \PDO $db,private array $config){}
 public function reschedule(array $input):array {
  BookingWriteFeatureGate::requireEnabled($this->config);
  BookingMutationConnectionGate::assertReady($this->db,$this->config);
  BookingOwnerIdentity::fromTenantConfig($this->config);
  $cmd=BookingRescheduleCommand::parse($input);
  BookingRescheduleFuturePolicy::assertFuture($cmd['window'],time());
  BookingTemporalConsistency::assertWindow($cmd['window'],$cmd['timezone']);
  $id=$cmd['booking_id'];
  $locks=new BookingLockManager($this->db);
  // One bounded budget covers booking-lock acquisition, record read and staff locks.
  $lockDeadlineNs=hrtime(true)+5000000000;
  // Acquire the booking lock first to serialize old/new staff identification.
  try {
   $locks->acquire(BookingTenantMutationLocks::cancellation($this->db,$id),5,$lockDeadlineNs);
   $old=(new BookingRescheduleRecordReader($this->db))->get($id);
   $locks->acquire(BookingTenantMutationLocks::staffUnion($this->db,$old['staff_ids'],$cmd['staff_ids']),5,$lockDeadlineNs);
   return (new BookingTransaction($this->db))->run(function()use($id,$old,$cmd,$input){
    $ledger=new BookingMutationLedger($this->db);
    $fingerprint=BookingMutationFingerprint::of('reschedule',BookingOwnerIdentity::fromTenantConfig($this->config),$cmd);
    $replayed=BookingMutationReplay::evaluate($ledger->forUpdate($cmd['idempotency_key']),$fingerprint);
    if($replayed!==null){if($replayed!==$id)throw new \DomainException('Reschedule replay refers to another booking');(new BookingMutationStateProof($this->db))->verifyReplay($replayed,'reschedule',$cmd);return ['booking_id'=>$replayed,'status'=>'rescheduled','replayed'=>true];}
    $current=(new BookingMutationReader($this->db))->locked($id);
    if($current['status']!==$old['status']||$current['staff_ids']!==$old['staff_ids']||$current['start']!==$old['start']||$current['order_id']!==$old['order_id'])throw new \DomainException('Booking changed concurrently');
    BookingRescheduleState::assertEligible($current['status']);
    // Revalidate after the booking and staff locks, not only before waiting.
    BookingRescheduleFuturePolicy::assertFuture($cmd['window'],time());
    $orderReader=new BookingRescheduleOrderReader($this->db);
    $stored=$orderReader->locked($current['order_id']);
    $orderReader->assertSingleBooking($current['order_id'],$id);
    // Conserve Cleanto shared-order duration. No duration change during reschedule.
    BookingRescheduleDurationPolicy::assertCompatible($stored['duration_minutes'],(int)$input['duration_minutes'],false);
    $future=['window'=>$cmd['window'],'staff_ids'=>$cmd['staff_ids'],'timezone'=>$cmd['timezone'],'duration_minutes'=>(int)$input['duration_minutes']];
    (new BookingFinalAvailability($this->db))->assertAvailable($future,$id);
    // Guard the mutation again if availability evaluation crossed the deadline.
    BookingRescheduleFuturePolicy::assertFuture($cmd['window'],time());
    $ledger->claim($cmd['idempotency_key'],$fingerprint);
    (new BookingRescheduleWriter($this->db))->update($id,$current['status'],$cmd['window'],$cmd['staff_ids']);
    (new BookingMutationStateProof($this->db))->verifyRescheduled($id,$cmd['window']['local_start'],$cmd['staff_ids']);
    $ledger->complete($cmd['idempotency_key'],$id);
    return ['booking_id'=>$id,'status'=>'rescheduled','payment_status'=>'unchanged'];
   });
  }finally{$locks->releaseAll();}
 }
}
