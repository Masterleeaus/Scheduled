<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Pass 38: persisted booking/order integrity. */
final class CommitReplayOrderProof {
 public static function assert(\PDO $db,int $bookingId,array $selection):void {
  CommitPersistenceProof::assert($db,$bookingId,$selection);
 }
}
