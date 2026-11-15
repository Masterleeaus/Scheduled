<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Explicit acknowledgement is required before test records can be committed. */
final class LifecycleConsent { public const TOKEN='I_UNDERSTAND_THIS_WRITES_TEST_DATA'; public static function assert(array $env):void {if(($env['BOOKING_LIFECYCLE_ACK']??null)!==self::TOKEN)throw new \DomainException('Explicit disposable-database write acknowledgement required');} }
