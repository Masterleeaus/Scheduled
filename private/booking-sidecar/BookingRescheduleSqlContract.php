<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Audit the exact legacy tables and lock requirements before runtime enablement. */
final class BookingRescheduleSqlContract {
 public static function requiredTables():array {return ['ct_bookings','ct_order_client_info','titan_booking_requests'];}
 public static function canMutate(array $verified):bool {
  foreach(self::requiredTables() as $table)if(($verified[$table]??false)!==true)return false;
  return ($verified['row_locking']??false)===true&&($verified['staff_locking']??false)===true;
 }
}
