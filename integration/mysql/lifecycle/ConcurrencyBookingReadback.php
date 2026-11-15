<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Strictly verify a single committed winning appointment row. */
final class ConcurrencyBookingReadback {
 public static function read(\PDO $db,int $bookingId):array {
  if($bookingId<1||$bookingId>2147483647)throw new \InvalidArgumentException('Invalid booking id');
  return AcceptanceReadbackSql::exactlyOne($db,'SELECT id,service_id,booking_date_time,staff_ids,booking_status FROM ct_bookings WHERE id=? LIMIT 2',[$bookingId],'winning appointment');
 }
}
