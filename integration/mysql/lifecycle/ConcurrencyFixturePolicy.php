<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyFixturePolicy {
 public static function assert(array $fixture):void {
  LifecycleFixtureValidator::validate($fixture);LifecycleCustomerSafety::assert($fixture['create']);
  if(count($fixture['create']['staff_ids']??[])<1)throw new \DomainException('Staff required for competing bookings');
  if(!isset($fixture['create']['start_at'],$fixture['create']['duration_minutes']))throw new \DomainException('Window required');
 }
}
