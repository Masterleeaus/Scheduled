<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Pass 38: persisted booking/order integrity. */
final class CommitCustomerOrder {
 public static function validate(array $booking,array $order):void {
  if(CommitBookingId::parse($booking['order_id']??null)!==CommitBookingId::parse($order['order_id']??null))throw new \RuntimeException('Customer order link mismatch');
  foreach(['client_name','client_email','client_phone'] as $field){if(!is_string($order[$field]??null)||trim($order[$field])==='')throw new \RuntimeException('Missing order contact');}
 }
}
