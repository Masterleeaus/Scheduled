<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Explicit supported checkout scope; do not silently drop extra requested services. */
final class BookingCreationPolicy {
 public static function assertScope(array $raw,array $command):void {
  foreach(['addons','coupon','tax','deposit','travel_fee','payment_token','recurrence','discount'] as $field){
   if(array_key_exists($field,$raw) && $raw[$field]!==null && $raw[$field]!==false && $raw[$field]!==[] && $raw[$field]!=='')
    throw new \DomainException('Unsupported booking modifier: '.$field);
  }
  if(($command['window']['start']??0)<=time())throw new \DomainException('Appointment must be in future');
  if(($command['window']['start']??0)>time()+366*86400)throw new \DomainException('Appointment too far ahead');
 }
}
