<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Confirm coordinator responses use real legacy signed-32-bit appointment identifiers. */
final class LifecycleBookingResult {
 public static function created(array $result):int {
  $id=$result['booking_id']??null;
  if(($result['status']??null)!=='active'||($result['replayed']??null)!==false||!is_int($id)||$id<1||$id>2147483647)throw new \RuntimeException('Booking was not confirmed');
  return $id;
 }
 public static function replay(array $result,int $id):void {
  if($id<1||$id>2147483647||($result['replayed']??null)!==true||($result['booking_id']??null)!==$id)throw new \RuntimeException('Idempotent replay disagrees with committed booking');
 }
}
