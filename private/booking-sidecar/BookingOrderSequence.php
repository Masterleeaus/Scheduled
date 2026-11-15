<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Distinct order IDs; seed titan_booking_orders to exceed existing Cleanto orders at provisioning. */
final class BookingOrderSequence {
 public function __construct(private \PDO $db){}
 public function next():int {
  if($this->db->exec('INSERT INTO titan_booking_orders () VALUES ()')!==1)throw new \RuntimeException('Order allocation insert failed');
  $id=(int)$this->db->lastInsertId();
  if($id<1)throw new \RuntimeException('Unable to allocate order');
  BookingLiveWriteIds::order($id);
  $s=$this->db->prepare('SELECT 1 FROM ct_bookings WHERE order_id=? UNION SELECT 1 FROM ct_order_client_info WHERE order_id=? LIMIT 1');
  if($s===false||$s->execute([$id,$id])!==true)throw new \RuntimeException('Order collision check failed');
  if($s->fetchColumn())throw new \DomainException('Order sequence not provisioned above existing Cleanto orders');
  return $id;
 }
}
