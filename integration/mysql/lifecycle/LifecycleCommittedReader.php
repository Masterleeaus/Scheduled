<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Read committed data through an independent database session, with bounded proof. */
final class LifecycleCommittedReader {
 public static function read(\PDO $db,int $id):array {
  if($id<1||$id>2147483647)throw new \InvalidArgumentException('Invalid booking identifier');
  $row=AcceptanceReadbackSql::exactlyOne($db,'SELECT id,order_id,service_id,staff_ids,booking_date_time,booking_status FROM ct_bookings WHERE id=? LIMIT 2',[$id],'committed booking');
  return LifecyclePersistedRow::normalize($row);
 }
}
