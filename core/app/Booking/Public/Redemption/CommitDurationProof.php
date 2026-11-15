<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Pass 38: persisted booking/order integrity. */
final class CommitDurationProof {
 public static function validate(array $booking,array $order,array $selection):void {
  CommitOrderRow::validate($order,CommitBookingId::parse($booking['order_id']??null),(int)$selection['duration_minutes']);
  CommitCustomerOrder::validate($booking,$order);
 }
}
