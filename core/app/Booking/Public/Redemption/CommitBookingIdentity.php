<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Pass 38: persisted booking/order integrity. */
final class CommitBookingIdentity {
 public static function validate(array $row,int $bookingId):array {
  if(CommitBookingId::parse($row['id']??null)!==$bookingId)throw new \RuntimeException('Booking identity mismatch');
  CommitBookingOrderPolicy::requireIds($row);
  CommitBookingStatusPolicy::requireActive($row['booking_status']??null);
  return $row;
 }
}
