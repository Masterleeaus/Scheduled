<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Stable client-side capability declaration. */
final class BookingOperationStatus {
 public static function forTenant(array $settings):array {
  $enabled=BookingWriteFeatureGate::enabled($settings);
  return ['service_catalogue'=>true,'preview'=>true,'owner_list'=>true,'owner_create'=>$enabled,'owner_cancel'=>$enabled,'owner_reschedule'=>$enabled,'public_checkout'=>false,'payments'=>false,'recurring_creation'=>false];
 }
}
