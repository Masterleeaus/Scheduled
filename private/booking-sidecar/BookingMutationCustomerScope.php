<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Customer cancellation must establish ownership via server-authenticated Cleanto customer ID. */
final class BookingMutationCustomerScope {
 public function __construct(private \PDO $db){}
 public function assertBookingOwner(int $bookingId,int $customerId):void {
  if(!$this->db->inTransaction())throw new \LogicException('Customer ownership requires transaction');
  if($bookingId<1||$customerId<1||$bookingId>2147483647||$customerId>2147483647)throw new \InvalidArgumentException('Invalid ownership lookup');
  $s=$this->db->prepare('SELECT id FROM ct_bookings WHERE id=? AND client_id=? LIMIT 2');
  if($s===false||$s->execute([$bookingId,$customerId])!==true)throw new \RuntimeException('Customer ownership query failed');
  $rows=$s->fetchAll(\PDO::FETCH_ASSOC);if(!is_array($rows))throw new \RuntimeException('Customer ownership rows unavailable');if(count($rows)!==1||BookingPositiveId::parse($rows[0]['id']??null)!==$bookingId)throw new \DomainException('Customer does not own booking');
 }
}
