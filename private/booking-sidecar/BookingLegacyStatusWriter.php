<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;use DomainException;
/** Optimistic status transition protects against late or concurrent mutations. */
final class BookingLegacyStatusWriter {
 public function __construct(private PDO $db){}
 public function cancel(int $id,string $current,string $target,string $reason):void {
  BookingPositiveId::parse($id);
  if(!$this->db->inTransaction())throw new \LogicException('Status mutation requires transaction');
  BookingStateMachine::transition($current,$target);
  if(strlen($reason)>200)throw new \InvalidArgumentException('Reason too long');
  $s=$this->db->prepare('UPDATE ct_bookings SET booking_status=?,reject_reason=?,lastmodify=NOW() WHERE id=? AND booking_status=?');
  if($s===false||$s->execute([$target,$reason,$id,$current])!==true)throw new \RuntimeException('Booking status update failed');if($s->rowCount()!==1)throw new DomainException('Booking changed concurrently');
 }
}
