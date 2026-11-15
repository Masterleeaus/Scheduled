<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Never accept an idempotent replay unless persisted booking still matches the new request. */
final class CommitReplaySelectionEvidence {
 public static function check(array $row,array $selection,int $bookingId):void {
  if(CommitBookingId::parse($row['id']??null)!==$bookingId)throw new \RuntimeException('Replayed booking ID mismatch');
  CommitLegacyProof::verify($row,CommitSelection::parse($selection));
  CommitBookingStatusPolicy::requireActive($row['booking_status']??null);
 }
}
