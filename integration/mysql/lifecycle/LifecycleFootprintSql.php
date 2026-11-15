<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Fixed-identifier read-only aggregate queries; never accept a table name from input. */
final class LifecycleFootprintSql {
 public const BOOKINGS='SELECT COUNT(*) AS total FROM ct_bookings';
 public const ORDERS='SELECT COUNT(*) AS total FROM ct_order_client_info';
 public static function count(\PDO $db,string $query):int {
  if(!in_array($query,[self::BOOKINGS,self::ORDERS],true))throw new \InvalidArgumentException('Unsupported count query');
  $statement=$db->prepare($query);
  if($statement===false||$statement->execute()!==true)throw new \RuntimeException('Footprint query failed');
  $rows=$statement->fetchAll(\PDO::FETCH_ASSOC);
  if(!is_array($rows)||count($rows)!==1||!is_array($rows[0])||array_keys($rows[0])!==['total'])throw new \RuntimeException('Invalid footprint aggregate');
  return LifecycleFootprintNumber::parse($rows[0]['total']);
 }
}
