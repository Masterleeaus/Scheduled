<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
final class BookingReschedule {
 public static function parse(array $input):array {
  $id=$input['booking_id']??null;$duration=$input['duration_minutes']??null;
  if(!is_int($id)||$id<1||!is_int($duration))throw new InvalidArgumentException('Invalid reschedule');
  if(!is_string($input['start_at']??null)||!is_string($input['timezone']??null))throw new InvalidArgumentException('Invalid time');
  return ['booking_id'=>$id,'window'=>BookingTimeWindow::parse($input['start_at'],$duration,$input['timezone']),'staff_ids'=>BookingStaff::parse($input['staff_ids']??null),'idempotency_key'=>BookingKey::normalize($input['idempotency_key']??null)];
 }
}
