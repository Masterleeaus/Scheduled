<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Stable fingerprint links a key to its exact actor, operation and command. */
final class BookingMutationFingerprint {
 public static function of(string $operation,array $actor,array $command):string {
  if(!in_array($operation,['cancel','reschedule'],true))throw new \InvalidArgumentException('Invalid mutation');
  return BookingKey::fingerprint(['operation'=>$operation,'actor'=>BookingMutationActor::normalize($actor),'command'=>$command]);
 }
}
