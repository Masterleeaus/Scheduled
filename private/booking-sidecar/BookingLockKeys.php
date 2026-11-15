<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Locks serialize all reservation writes for each selected staff member. */
final class BookingLockKeys {
 public static function forStaff(array $ids):array {
  $ids=BookingStaff::parse($ids); $keys=[];
  foreach($ids as $id){$keys[]='tb:staff:'.$id;}
  sort($keys,SORT_STRING);return $keys;
 }
 public static function forTenantStaff(string $tenant,array $ids):array {
  $prefix=BookingLockNamespace::validate($tenant);$keys=[];
  foreach(BookingStaff::parse($ids) as $id)$keys[]='tb:'.$prefix.':staff:'.$id;
  sort($keys,SORT_STRING);return $keys;
 }
 public static function forTenantBooking(string $tenant,int $id):string {
  if($id<1||$id>2147483647)throw new \InvalidArgumentException('Booking id out of legacy range');
  return 'tb:'.BookingLockNamespace::validate($tenant).':booking:'.$id;
 }
 public static function forBooking(int $id):string {
  if($id<1||$id>2147483647)throw new \InvalidArgumentException('Booking id out of legacy range');
  return 'tb:booking:'.$id;
 }
}
