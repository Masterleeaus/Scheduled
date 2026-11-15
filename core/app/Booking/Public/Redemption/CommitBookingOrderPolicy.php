<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitBookingOrderPolicy {
 public static function requireIds(array $row):void {
  foreach(['id','client_id','order_id'] as $key)CommitBookingId::parse($row[$key]??null);
 }
}
