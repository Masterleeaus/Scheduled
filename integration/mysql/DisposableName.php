<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class DisposableName {
 public static function assert(string $name):void {
  if(!preg_match('/^titan_booking_test_[a-z0-9_]{1,32}$/D',$name))throw new \DomainException('A dedicated titan_booking_test_ database is required');
 }
}
