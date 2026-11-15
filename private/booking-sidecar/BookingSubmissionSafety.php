<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Single place to reject client-supplied authority, rates and payment assertions. */
final class BookingSubmissionSafety {
 public static function assertSafe(array $input):void {
  foreach(['tenant_id','tenant_database','database','owner_id','booking_id','payment_status','paid','total','total_price','price','amount','confirmed','booking_status','verified_pricing_settings','booking_writes_enabled'] as $key){
   if(array_key_exists($key,$input))throw new \InvalidArgumentException('Booking submission contains privileged field');
  }
 }
}
