<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** An overlapping second command must be rejected, not assigned a new booking ID. */
final class LifecycleConflictAssertion { public static function check(callable $attempt):void {try{$attempt();}catch(\DomainException $e){if(preg_match('/(conflict|unavailable|already booked|pending)/i',$e->getMessage()))return;throw new \RuntimeException('Conflicting booking rejected for unrelated reason',0,$e);}throw new \RuntimeException('Double booking was not rejected');} }
