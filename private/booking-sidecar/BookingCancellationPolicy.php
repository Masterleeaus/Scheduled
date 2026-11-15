<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Cleanto terminal statuses cannot be cancelled a second time. */
final class BookingCancellationPolicy {
 public static function target(string $current,string $role):string {
  $target=$role==='customer'?'CC':($role==='owner'?'CS':'');
  if(!$target)throw new \DomainException('Unknown cancellation role');
  return BookingStateMachine::transition($current,$target);
 }
}
