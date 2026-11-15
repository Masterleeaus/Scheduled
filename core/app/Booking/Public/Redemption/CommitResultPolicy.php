<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitResultPolicy {
 public static function confirmed(array $response):void {
  if(($response['confirmed']??false)!==true)throw new \RuntimeException('No booking confirmation');
  CommitBookingId::parse($response['booking_id']??null);
  if(($response['payment_settled']??false)===true)throw new \RuntimeException('Cannot infer payment settlement');
 }
}
