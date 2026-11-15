<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Safe structured record with no customer PII or secrets. */
final class BookingAuditEvent {
 public static function record(string $type,string $instance,int $bookingId,string $key):array {
  if(!in_array($type,['requested','rejected','confirmed','cancelled','rescheduled'],true)||$bookingId<0)throw new InvalidArgumentException('Invalid audit event');
  return ['type'=>$type,'instance'=>$instance,'booking_id'=>$bookingId,'request_hash'=>hash('sha256',$key)];
 }
}
