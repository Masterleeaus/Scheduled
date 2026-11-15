<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicAvailabilityGuard {
 public static function assertReadOnly(string $method,array $body):void {
  if(strtoupper($method)!=='POST')throw new \InvalidArgumentException('Availability requires POST');
  foreach(['booking_id','customer_id','tenant_id','owner_id','payment','confirm','status','price','api_key'] as $key)if(array_key_exists($key,$body))throw new \InvalidArgumentException('Booking writes are disabled');
 }
}
