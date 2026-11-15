<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Run every fail-closed check before opening the first write transaction. */
final class LifecyclePreflight { public static function inspect(array $env,array $cfg,array $fixture):void {LifecycleConsent::assert($env);LifecycleFixtureValidator::validate($fixture);LifecycleTestScope::assert($cfg,$fixture);LifecycleCustomerSafety::assert($fixture['create']);if(!LifecycleMutationKeys::distinct(LifecycleMutationKeys::generate()))throw new \LogicException('Mutation keys collided');} }
