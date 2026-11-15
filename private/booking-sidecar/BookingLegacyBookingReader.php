<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;use DomainException;
final class BookingLegacyBookingReader {
 public function __construct(private PDO $db){}
 public function getForUpdate(int $bookingId):array {
  if($bookingId<1)throw new \InvalidArgumentException('Invalid booking id');
  return BookingLockedRowProof::fetch($this->db,$bookingId,true);
 }
}
