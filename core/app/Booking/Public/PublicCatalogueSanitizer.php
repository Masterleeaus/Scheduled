<?php
namespace App\Booking\Public;
final class PublicCatalogueSanitizer {
 public static function onlyAllowed(array $response):array {
  $allowed=['services','methods','units','staff','preview_only','booking_writes_enabled'];
  return array_intersect_key($response,array_flip($allowed));
 }
}
