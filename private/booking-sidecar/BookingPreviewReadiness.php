<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewReadiness {
 public static function response():array {
  return ['supports_quote'=>true,'supports_staff_slots'=>true,'creates_booking'=>false,'writes_enabled'=>false,'database_test_required'=>true];
 }
}
