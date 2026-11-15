<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Explicit runtime gates; source-level checks do not enable public mutations. */
final class BookingRescheduleReadiness {
 public static function inspect(array $checks):array {
  $required=['mysql_integration','concurrent_reservations','pricing_parity','staff_schedule','tenant_scope','shared_order_parity'];
  $missing=array_values(array_filter($required,fn($name)=>($checks[$name]??false)!==true));
  return ['ready'=>count($missing)===0,'missing'=>$missing,'public_enabled'=>false];
 }
}
