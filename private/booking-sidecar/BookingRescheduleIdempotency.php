<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Keep request identity separate from create/cancel, and bind to actor. */
final class BookingRescheduleIdempotency {
 public static function key(string $key):string {return 'reschedule_'.substr(hash('sha256',BookingKey::normalize($key)),0,48);}
 public static function fingerprint(array $actor,array $command):string {
  return BookingMutationFingerprint::of('reschedule',$actor,$command);
 }
}
