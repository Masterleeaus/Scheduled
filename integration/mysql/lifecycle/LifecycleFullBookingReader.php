<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Independently verify exactly one persisted Cleanto appointment. */
final class LifecycleFullBookingReader {
 public static function read(\PDO $db,int $id):array {
  if($id<1||$id>2147483647)throw new \InvalidArgumentException('Invalid booking identifier');
  $row=AcceptanceReadbackSql::exactlyOne($db,'SELECT id,order_id,client_id,service_id,method_id,method_unit_id,method_unit_qty,method_unit_qty_rate,staff_ids,booking_date_time,booking_status FROM ct_bookings WHERE id=? LIMIT 2',[$id],'booking');
  return LifecycleFullBookingRow::parse($row);
 }
}
