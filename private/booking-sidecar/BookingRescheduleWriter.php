<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Optimistic mutation of Cleanto booking time/staff/status, under row and staff locks. */
final class BookingRescheduleWriter {
 public function __construct(private \PDO $db){}
 public function update(int $id,string $originalStatus,array $window,array $staffIds):void {
  if(!$this->db->inTransaction())throw new \LogicException('Transaction required');
  BookingPositiveId::parse($id);
  $target=BookingRescheduleState::next($originalStatus);
  $s=$this->db->prepare('UPDATE ct_bookings SET booking_date_time=?,staff_ids=?,booking_status=?,lastmodify=NOW() WHERE id=? AND booking_status=?');
  if($s===false||$s->execute([$window['local_start'],BookingStaff::toLegacyCsv($staffIds),$target,$id,$originalStatus])!==true)throw new \RuntimeException('Reschedule update failed');
  if($s->rowCount()!==1)throw new \DomainException('Booking changed concurrently');
 }
}
