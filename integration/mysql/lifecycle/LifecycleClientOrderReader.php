<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Independent customer-order metadata proof without writes. */
final class LifecycleClientOrderReader {
 public static function read(\PDO $db,int $orderId):array {
  if($orderId<1||$orderId>2147483647)throw new \InvalidArgumentException('Invalid order identifier');
  $row=AcceptanceReadbackSql::exactlyOne($db,'SELECT id,order_id,order_duration,client_name,client_email,client_phone FROM ct_order_client_info WHERE order_id=? LIMIT 2',[$orderId],'customer order');
  return LifecycleClientOrderRow::parse($row);
 }
}
