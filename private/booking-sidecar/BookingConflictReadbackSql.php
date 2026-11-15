<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Database-projected busy rows; joining duration from Cleanto's order table. */
final class BookingConflictReadbackSql {
 public static function query():string {
  return "SELECT b.id,b.order_id,b.booking_date_time,b.staff_ids,b.booking_status,o.order_duration FROM ct_bookings b INNER JOIN ct_order_client_info o ON o.order_id=b.order_id WHERE b.booking_date_time >= ? AND b.booking_date_time < ? AND b.booking_status IN ('A','C','RS') AND FIND_IN_SET(?,REPLACE(b.staff_ids,' ','')) > 0 ORDER BY b.booking_date_time,b.id LIMIT 5001";
 }
}
