<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** A replay read is locked and tenant-scoped through the provided tenant PDO. */
final class CommitReplayPersistedReader {
 public static function read(\PDO $db,int $id):array {
  if($id<1)throw new \InvalidArgumentException('Invalid booking');
  CommitTransactionBoundary::assert($db);
  return CommitLegacyEvidenceReader::read($db,$id);
 }
}
