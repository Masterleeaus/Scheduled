<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** No customer-supplied owner, database, paid flag or booking identifier. */
final class CommitLegacyIdentity {
 public static function check(array $payload):void {
  foreach(['owner_id','tenant_id','database','booking_id','payment_status','paid','is_admin','rate','price','total'] as $key){
   if(array_key_exists($key,$payload))throw new \DomainException('Privileged booking input denied');
  }
 }
}
