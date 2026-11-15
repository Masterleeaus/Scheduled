<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitIntentReplayPolicy {
 public static function requireSameIntent(array $ledger,int $owner,string $fingerprint):int {
  if(CommitLedgerState::validate($ledger,$owner,$fingerprint)!=='committed')throw new \RuntimeException('Replay is not committed');
  return CommitBookingId::parse($ledger['booking_id']??null);
 }
}
