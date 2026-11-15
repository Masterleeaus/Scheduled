<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Read and validate exactly one locked, tenant-local legacy booking row. */
final class BookingLockedRowProof {
 public static function fetch(\PDO $db,int $bookingId,bool $withCustomer):array {
  if($bookingId<1||$bookingId>2147483647)throw new \InvalidArgumentException('Invalid booking ID');
  if(!$db->inTransaction())throw new \LogicException('Booking read requires transaction');
  $columns=$withCustomer?'id,order_id,client_id,booking_date_time,booking_status,staff_ids':'id,order_id,staff_ids,booking_date_time,booking_status';
  $s=$db->prepare('SELECT '.$columns.' FROM ct_bookings WHERE id=? FOR UPDATE');
  if($s===false||$s->execute([$bookingId])!==true)throw new \RuntimeException('Locked booking query failed');
  $row=$s->fetch(\PDO::FETCH_ASSOC);
  if(!is_array($row))throw new \DomainException('Booking not found');
  if($s->fetch(\PDO::FETCH_ASSOC)!==false)throw new \DomainException('Duplicate booking records');
  if(BookingWriteProofId::check($row['id']??null)!==$bookingId)throw new \DomainException('Wrong locked booking');
  return $row;
 }
}
