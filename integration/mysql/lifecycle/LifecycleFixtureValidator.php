<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Validate fixture shape before attempting any SQL writes. */
final class LifecycleFixtureValidator { public static function validate(array $fixture):array { foreach(['database','environment','tenant','create','reschedule'] as $k)if(!array_key_exists($k,$fixture))throw new \InvalidArgumentException('Missing fixture field: '.$k);foreach(['tenant','create','reschedule'] as $k)if(!is_array($fixture[$k])||array_is_list($fixture[$k]))throw new \InvalidArgumentException('Invalid fixture section: '.$k);if(!is_string($fixture['database'])||!is_string($fixture['environment']))throw new \InvalidArgumentException('Invalid fixture metadata');$tenant=$fixture['tenant'];foreach(['authenticated_owner_id','verified_pricing_settings'] as $k)if(!array_key_exists($k,$tenant))throw new \InvalidArgumentException('Missing tenant setting: '.$k);if(!is_int($tenant['authenticated_owner_id'])||$tenant['authenticated_owner_id']<1||!is_array($tenant['verified_pricing_settings']))throw new \InvalidArgumentException('Invalid tenant verification settings');$create=$fixture['create'];$create['idempotency_key']='fixture-validation-0001';
  \Titan\BookingSidecar\BookingCommand::create($create);
  $reschedule=$fixture['reschedule'];$reschedule['booking_id']=1;$reschedule['idempotency_key']='fixture-validation-0002';
  \Titan\BookingSidecar\BookingRescheduleCommand::parse($reschedule);
  return $fixture;} }
