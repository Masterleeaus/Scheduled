<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Existing Cleanto persistence adapters, executed inside the caller's claim transaction. */
final class BookingAtomicPersist {
 public function __construct(private \PDO $db,private array $settings){}
 public function insert(array $raw):int {
  if(!$this->db->inTransaction())throw new \RuntimeException('Booking claim transaction required');
  BookingSubmissionSafety::assertSafe($raw);
  $cmd=BookingCommand::create($raw);
  BookingCreationPolicy::assertScope($raw,$cmd);
  BookingMutationTimeGuard::assertFuture($cmd['window'],time());
  $rate=(new BookingServiceRateGuard($this->db,$this->settings))->confirmedRate($cmd['service']);
  (new BookingFinalAvailability($this->db))->assertAvailable($cmd);
  (new BookingConflictGuard($this->db))->assertFree($cmd);
  $order=(new BookingOrderSequence($this->db))->next();
  $client=(new BookingLegacyClientWriter($this->db))->insert($order,$cmd['customer'],$cmd['duration_minutes'],$cmd['personal_info']);
  $id=(new BookingLegacyAppointmentWriter($this->db))->insert($order,$client,$cmd,$rate);
  BookingAtomicPersistProof::verify($this->db,$id,$cmd,$rate);
  return $id;
 }
}
