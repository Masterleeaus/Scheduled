<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Booking cancellation command; no tenant identity or status accepted from callers. */
final class BookingCancellationInput {
 public static function parse(array $p):array {
  $id=$p['booking_id']??null;$key=BookingKey::normalize($p['idempotency_key']??null);
  if(!is_int($id)||$id<1||$id>2147483647)throw new \InvalidArgumentException('Invalid booking ID');
  $reason=$p['reason']??'';
  if(!is_string($reason)||strlen($reason)>200||preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/',$reason))throw new \InvalidArgumentException('Invalid cancellation reason');
  return ['booking_id'=>$id,'idempotency_key'=>$key,'reason'=>$reason];
 }
}
