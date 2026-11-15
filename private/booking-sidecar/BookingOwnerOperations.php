<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Operations reachable only via an authenticated business-owner gateway. */
final class BookingOwnerOperations {
 public function __construct(private \PDO $db,private array $settings){}
 public function dispatch(string $operation,array $payload):array {
  BookingOwnerOperationPolicy::assertAllowed($operation,$this->settings);
  if(in_array($operation,['owner_booking_create','owner_booking_cancel','owner_booking_reschedule'],true))BookingMutationConnectionGate::assertReady($this->db,$this->settings);
  return match($operation){
   'owner_booking_list'=>(new BookingOwnerListing($this->db))->get($payload),
   'owner_booking_summary'=>(new BookingOwnerSummary($this->db))->get($payload),
   'owner_booking_get'=>(new BookingOwnerLookup($this->db))->one((int)($payload['booking_id']??0)),
   'owner_booking_create'=>BookingPaymentBoundary::forBooking((new BookingWriteCoordinator($this->db,$this->settings))->create($payload)),
   'owner_booking_cancel'=>(new BookingCancellationCoordinator($this->db,$this->settings))->cancel($payload,BookingOwnerIdentity::fromTenantConfig($this->settings)),
   'owner_booking_reschedule'=>(new BookingRescheduleCoordinator($this->db,$this->settings))->reschedule($payload),
   default=>throw new \InvalidArgumentException('Unknown owner operation')
  };
 }
}
