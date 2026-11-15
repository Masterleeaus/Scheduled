<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Pass 38: persisted booking/order integrity. */
final class CommitOrderReader {
 public static function read(\PDO $db,int $orderId):array {
  if($orderId<1)throw new \InvalidArgumentException('Invalid order id');
  CommitTransactionBoundary::assert($db);
  $s=$db->prepare('SELECT order_id,order_duration,client_name,client_email,client_phone FROM ct_order_client_info WHERE order_id=? LIMIT 2 FOR UPDATE');
  if(!$s||!$s->execute([$orderId]))throw new \RuntimeException('Order query failed');
  $rows=$s->fetchAll(\PDO::FETCH_ASSOC);
  if(count($rows)!==1)throw new \RuntimeException('Expected one customer order');
  return $rows[0];
 }
}
