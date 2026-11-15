<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Fail-closed staff snapshot taken after the booking advisory lock. */
final class BookingRescheduleRecordReader {
 public function __construct(private \PDO $db){}
 public function get(int $id):array {
  BookingPositiveId::parse($id);
  $s=$this->db->prepare('SELECT id,order_id,booking_status,booking_date_time,staff_ids FROM ct_bookings WHERE id=?');
  if($s===false||$s->execute([$id])!==true)throw new \RuntimeException('Reschedule snapshot query failed');
  $row=$s->fetch(\PDO::FETCH_ASSOC);
  if($row===false)throw new \DomainException('Unknown booking');
  if(!is_array($row)||$s->fetch(\PDO::FETCH_ASSOC)!==false)throw new \DomainException('Ambiguous booking snapshot');
  $result=BookingMutationRecord::parse($row);
  if($result['id']!==$id)throw new \DomainException('Wrong booking snapshot');
  return $result;
 }
}
