<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Uses the tenant-resolved PDO connection, never a caller-provided tenant identifier. */
final class BookingMutationReader {
 public function __construct(private \PDO $db){}
 public function locked(int $bookingId):array {
  if($bookingId<1||$bookingId>2147483647)throw new \InvalidArgumentException('Invalid booking ID');
  if(!$this->db->inTransaction())throw new \LogicException('Booking read requires transaction');
  return BookingMutationRecord::parse(BookingLockedRowProof::fetch($this->db,$bookingId,false));
 }
}
