<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Unsafe flags are ONLY supplied in a disposable acceptance process; no production authorization is implied. */
final class AcceptanceTestSettings {
 public static function build(array $cfg,array $fixture,array $env):array {
  LifecycleConsent::assert($env);LifecycleTestScope::assert($cfg,$fixture);
  if(($env['BOOKING_ACCEPT_TEST_ONLY']??null)!=='YES_DISPOSABLE_ONLY')throw new \DomainException('Test-only write override missing');
  $settings=$fixture['tenant'];$settings['database']=$cfg['database'];
  foreach(['booking_writes_enabled','booking_schema_verified','booking_rate_mapping_verified','booking_concurrency_verified'] as $key)$settings[$key]=true;
  $settings['acceptance_only_unverified']=true;
  return $settings;
 }
}
