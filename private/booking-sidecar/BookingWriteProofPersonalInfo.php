<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Validate the actual persisted Cleanto location payload before transaction commit.
 * Compares the exact canonical serialization used by BookingLegacyClientWriter.
 * Never unserializes untrusted database content into PHP objects.
 */
final class BookingWriteProofPersonalInfo {
 public static function check($stored,array $expected):void {
  if(!is_string($stored))throw new \DomainException('Missing persisted customer location');
  $canonical=BookingPersonalInfo::encode($expected);
  if(!hash_equals($canonical,$stored))throw new \DomainException('Persisted customer location mismatch');
 }
}
