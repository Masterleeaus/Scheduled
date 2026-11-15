<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Opt-in requires explicit per-tenant verified database and pricing configuration. */
final class BookingWriteFeatureGate {
 public static function enabled(array $config):bool {
  foreach(['booking_writes_enabled','booking_schema_verified','booking_rate_mapping_verified','booking_concurrency_verified'] as $flag)if(($config[$flag]??false)!==true)return false;
  return true;
 }
 public static function requireEnabled(array $config):void {if(!self::enabled($config))throw new \RuntimeException('Booking writes not enabled for this business');}
}
