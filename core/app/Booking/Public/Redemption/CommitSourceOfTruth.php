<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitSourceOfTruth {
 public static function requirePersisted(array $row,int $id):void {
  CommitEvidence::assert($row,$id);
  CommitBookingOrderPolicy::requireIds($row);
 }
}
