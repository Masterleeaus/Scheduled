<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Pass 38: persisted booking/order integrity. */
final class CommitOrderRow {
 public static function validate(array $row,int $orderId,int $duration):void {
  if(CommitBookingId::parse($row['order_id']??null)!==$orderId)throw new \RuntimeException('Order identity mismatch');
  if(CommitOrderDuration::minutes($row['order_duration']??null)!==$duration)throw new \RuntimeException('Order duration mismatch');
 }
}
