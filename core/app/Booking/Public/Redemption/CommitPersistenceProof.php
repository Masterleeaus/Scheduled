<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Pass 38: persisted booking/order integrity. */
final class CommitPersistenceProof {
 public static function assert(\PDO $db,int $bookingId,array $selection):void {
  CommitTransactionBoundary::assert($db);
  $booking=CommitLegacyEvidenceReader::read($db,$bookingId);
  CommitBookingIdentity::validate($booking,$bookingId);
  CommitLegacyProof::verify($booking,CommitSelection::parse($selection));
  $order=CommitOrderReader::read($db,CommitBookingId::parse($booking['order_id']));
  CommitDurationProof::validate($booking,$order,$selection);
 }
}
