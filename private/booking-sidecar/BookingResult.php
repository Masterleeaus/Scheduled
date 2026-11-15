<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
final class BookingResult {
 public static function response(int $bookingId,string $status,string $startAt):array {
  if($bookingId<1||!in_array($status,['A','C','RS','CC','CS'],true))throw new InvalidArgumentException('Invalid booking result');
  return ['booking_id'=>$bookingId,'status'=>$status,'start_at'=>$startAt];
 }
}
