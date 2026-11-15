<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Only statuses accepted by Cleanto for existing active appointments. */
final class BookingRescheduleState {
 public static function assertEligible(string $status):void {
  if(!in_array($status,['A','C','RS'],true))throw new \DomainException('Cannot reschedule this booking status');
 }
 public static function next(string $status):string {self::assertEligible($status);return 'RS';}
}
