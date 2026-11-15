<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** The legacy schema stores duration per order, not per booking. */
final class BookingRescheduleDurationPolicy {
 public static function assertCompatible(int $stored,int $requested,bool $singleBooking):void {
  if($stored<1||$requested<15||$requested>720)throw new \DomainException('Invalid duration');
  if(!$singleBooking&&$stored!==$requested)throw new \DomainException('Shared order duration cannot change');
 }
}
