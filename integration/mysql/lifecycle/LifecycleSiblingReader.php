<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Independent, bounded snapshot of siblings, never a mutation. */
final class LifecycleSiblingReader {
 public static function read(\PDO $db,int $orderId,int $excludeBookingId):array {
  if($orderId<1||$excludeBookingId<1)throw new \InvalidArgumentException('Invalid order/booking');
  $s=$db->prepare('SELECT id,order_id,booking_date_time,staff_ids,booking_status FROM ct_bookings WHERE order_id=? AND id<>? ORDER BY id LIMIT 501');
  if(!$s||!$s->execute([$orderId,$excludeBookingId]))throw new \RuntimeException('Sibling readback failed');
  $rows=$s->fetchAll(\PDO::FETCH_ASSOC);
  if(!is_array($rows)||count($rows)>500)throw new \RuntimeException('Sibling snapshot truncated');
  $out=[];
  foreach($rows as $raw){$row=LifecycleSiblingRow::parse($raw);if($row['order_id']!==$orderId||$row['id']===$excludeBookingId||isset($out[$row['id']]))throw new \RuntimeException('Invalid sibling result');$out[$row['id']]=$row;}
  ksort($out,SORT_NUMERIC);return $out;
 }
}
