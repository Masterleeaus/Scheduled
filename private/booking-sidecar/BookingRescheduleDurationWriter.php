<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Update order duration only after single-booking check and within the same transaction. */
final class BookingRescheduleDurationWriter {
 public function __construct(private \PDO $db){}
 public function update(int $orderId,int $minutes):void {
  if(!$this->db->inTransaction()||$minutes<15||$minutes>720)throw new \LogicException('Invalid duration update');
  $s=$this->db->prepare('UPDATE ct_order_client_info SET order_duration=? WHERE order_id=?');
  if($s===false||$s->execute([$minutes,$orderId])!==true)throw new \RuntimeException('Reschedule duration update failed');
  if($s->rowCount()!==1)throw new \DomainException('Order duration update did not affect exactly one row');
 }
}
