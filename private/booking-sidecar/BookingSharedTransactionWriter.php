<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Transaction-native entrypoint: never begins or commits caller's transaction. */
final class BookingSharedTransactionWriter {
 public function __construct(private array $tenant){}
 public function write(\PDO $db,array $payload):int {
  BookingAtomicPersistBoundary::assert($db);
  BookingSharedTransactionScope::assert($db,$this->tenant);
  BookingLiveWriteGuard::assert($db,$this->tenant);
  BookingWriteFeatureGate::requireEnabled($this->tenant);
  BookingTenantWritePreflight::assertReady($this->tenant);
  $command=BookingSharedCommand::normalize($payload);
  BookingLiveWriteCommandPolicy::assert($command);
  $rate=BookingSharedRate::authoritative($db,$command,$this->tenant);
  BookingAtomicPersistBoundary::assert($db);
  BookingSharedAvailability::assert($db,$command);
  BookingAtomicPersistBoundary::assert($db);
  // Pricing and availability may take time even after the first future-time check.
  // Revalidate at the last safe boundary before creating any order/customer rows.
  BookingMutationTimeGuard::assertFuture($command['window'],time());
  $id=BookingSharedResult::bookingId(BookingSharedPersist::write($db,$command,$rate));
  BookingAtomicPersistBoundary::assert($db);
  BookingWritePostCommitGate::verify($db,$id,$command,$rate);
  BookingAtomicPersistBoundary::assert($db);
  return $id;
 }
}
