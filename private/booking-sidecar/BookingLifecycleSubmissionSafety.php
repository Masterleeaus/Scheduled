<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/**
 * Rejects caller-supplied authority for cancel and reschedule. Owner identity, tenant, order,
 * status, prices and payment state come only from the server (tenant registry and stored rows).
 * booking_id and idempotency_key are the only identifiers a caller may send.
 */
final class BookingLifecycleSubmissionSafety {
 private const FORBIDDEN=[
  'actor','role','tenant_id','tenant_database','database','owner_id','authenticated_owner_id',
  'client_id','customer_id','order_id','status','booking_status','payment_status','paid','confirmed',
  'total','total_price','price','amount','rate','price_override','verified_pricing_settings',
  'booking_writes_enabled','owner_operations_enabled',
 ];
 public static function assertSafe(array $payload):void {
  foreach(self::FORBIDDEN as $key){
   if(array_key_exists($key,$payload))throw new \InvalidArgumentException('Lifecycle submission contains privileged field');
  }
 }
}
