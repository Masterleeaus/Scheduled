<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Restrict booking times to canonical IANA region identifiers; ban offset aliases. */
final class BookingTimeZonePolicy {
 public static function validate(string $zone):string {
  if($zone!=='UTC'&&!in_array($zone,\DateTimeZone::listIdentifiers(),true))throw new \InvalidArgumentException('Unsupported timezone');
  return $zone;
 }
}
