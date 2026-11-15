<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Owner can inspect an existing booking in its own tenant database. */
final class BookingOwnerLookup {
 public function __construct(private \PDO $db){}
 public function one(int $id):array {
  if($id<1)throw new \InvalidArgumentException('Invalid booking');
  $s=$this->db->prepare('SELECT b.id,b.order_id,b.booking_status,b.booking_date_time,b.staff_ids,b.client_id,b.service_id,b.method_id,b.method_unit_id,b.method_unit_qty,o.order_duration,o.client_name,o.client_email,o.client_phone FROM ct_bookings b INNER JOIN ct_order_client_info o ON o.order_id=b.order_id WHERE b.id=? LIMIT 1');
  $s->execute([$id]);$row=$s->fetch(\PDO::FETCH_ASSOC);
  if(!$row)throw new \DomainException('Booking not found');return ['booking'=>$row];
 }
}
