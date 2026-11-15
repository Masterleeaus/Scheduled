<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Prevent owner privileges being inferred from an HMAC signature alone. */
final class BookingOwnerOperationPolicy {
 public static function assertAllowed(string $operation,array $settings):void {
  $allowed=['owner_booking_list','owner_booking_get','owner_booking_create','owner_booking_cancel','owner_booking_reschedule','owner_booking_summary'];
  if(!in_array($operation,$allowed,true))throw new \InvalidArgumentException('Unknown owner operation');
  if(($settings['owner_operations_enabled']??false)!==true)throw new \DomainException('Owner operations not enabled for tenant');
  BookingOwnerIdentity::fromTenantConfig($settings);
  if(in_array($operation,['owner_booking_create','owner_booking_cancel','owner_booking_reschedule'],true))BookingWriteFeatureGate::requireEnabled($settings);
 }
}
