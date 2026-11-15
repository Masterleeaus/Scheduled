<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** NEVER infer readiness merely from operator-set flags. Require observed schema result. */
final class BookingSchemaReleaseGate {
 public static function assertReady(array $tenant,array $schema):void {
  BookingWriteFeatureGate::requireEnabled($tenant);
  if(($schema['ready']??false)!==true||($schema['errors']??[])!==[])throw new \DomainException('Booking schema audit not clean');
  foreach(['booking_schema_verified','booking_rate_mapping_verified','booking_concurrency_verified'] as $key)if(($tenant[$key]??false)!==true)throw new \DomainException('Verification flag absent: '.$key);
 }
}
