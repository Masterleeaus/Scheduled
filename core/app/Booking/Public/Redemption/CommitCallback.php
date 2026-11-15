<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Never accept a booking ID if its writer ended the transaction itself. */
final class CommitCallback {
 public static function call(callable $writer,array $selection,array $customer,\PDO $db):int {
  CommitTransactionBoundary::beforeWrite($db);
  $id=$writer($selection,$customer,$db);
  CommitTransactionBoundary::afterWrite($db);
  return CommitBookingId::parse($id);
 }
}
