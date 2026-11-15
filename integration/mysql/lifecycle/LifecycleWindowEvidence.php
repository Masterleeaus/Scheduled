<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** A reschedule must be to a different date/time in an acceptance fixture. */
final class LifecycleWindowEvidence {
 public static function assertDistinct(array $fixture):void {
  $start=$fixture['create']['start_at']??null;$move=$fixture['reschedule']['start_at']??null;
  if(!is_string($start)||!is_string($move)||$start===$move)throw new \DomainException('Lifecycle reschedule fixture must change appointment time');
 }
}
