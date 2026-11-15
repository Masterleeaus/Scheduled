<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Validate both isolated fixtures before launching ANY database acceptance probes. */
final class AcceptanceScopeGate {
 public static function assert(array $env,array $lifecycle,array $concurrency):array {
  $cfg=MysqlTestConfig::fromEnvironment($env);
  LifecycleConsent::assert($env);
  if(($env['BOOKING_ACCEPT_TEST_ONLY']??null)!=='YES_DISPOSABLE_ONLY')throw new \DomainException('Test-only override required');
  foreach(['lifecycle'=>$lifecycle,'concurrency'=>$concurrency] as $label=>$fixture){
   LifecycleFixtureValidator::validate($fixture);
   LifecycleTestScope::assert($cfg,$fixture);
   LifecycleCustomerSafety::assert($fixture['create']);
  }
  ConcurrencyFixturePolicy::assert($concurrency);
  AcceptanceWindowIsolation::assert($lifecycle,$concurrency);
  return $cfg;
 }
}
