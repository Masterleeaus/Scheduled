<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Locked duration and order cardinality proof for rescheduling. */
final class BookingRescheduleOrderReader {
 public function __construct(private \PDO $db){}
 private function boundary(int $orderId):void {if(!$this->db->inTransaction()||$orderId<1||$orderId>2147483647)throw new \LogicException('Order requires active transaction');}
 public function locked(int $orderId):array {
  $this->boundary($orderId);
  $s=$this->db->prepare('SELECT order_id,order_duration FROM ct_order_client_info WHERE order_id=? FOR UPDATE');
  if($s===false||$s->execute([$orderId])!==true)throw new \RuntimeException('Order duration lookup failed');
  $row=$s->fetch(\PDO::FETCH_ASSOC);
  if($row===false)throw new \DomainException('Order duration not found');
  if(!is_array($row)||$s->fetch(\PDO::FETCH_ASSOC)!==false)throw new \DomainException('Ambiguous order duration');
  if(BookingPositiveId::parse($row['order_id']??null)!==$orderId)throw new \DomainException('Wrong order duration');
  $duration=filter_var($row['order_duration']??null,FILTER_VALIDATE_INT);
  if($duration===false||$duration<1||$duration>43200)throw new \DomainException('Invalid order duration');
  return ['order_id'=>$orderId,'duration_minutes'=>(int)$duration];
 }
 public function assertSingleBooking(int $orderId,int $bookingId):void {
  $this->boundary($orderId);BookingPositiveId::parse($bookingId);
  $s=$this->db->prepare('SELECT id FROM ct_bookings WHERE order_id=? LIMIT 2 FOR UPDATE');
  if($s===false||$s->execute([$orderId])!==true)throw new \RuntimeException('Order booking lookup failed');
  $rows=$s->fetchAll(\PDO::FETCH_COLUMN);
  if(!is_array($rows)||count($rows)!==1||BookingPositiveId::parse($rows[0])!==$bookingId)throw new \DomainException('Shared order reschedule not supported');
 }
}
