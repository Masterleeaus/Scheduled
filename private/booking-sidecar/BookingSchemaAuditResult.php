<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Make incomplete schema and nontransactional engine explicit release blockers. */
final class BookingSchemaAuditResult {
 public static function evaluate(array $columns,array $indexes,array $engines):array {
  $errors=BookingSchemaColumnContract::missing($columns);
  array_push($errors,...array_map(static fn($v)=>'missing_unique_index:'.$v,BookingSchemaIndexContract::absent($indexes)));
  foreach(['ct_bookings','ct_order_client_info','titan_booking_requests','titan_booking_orders'] as $table)if(strtoupper((string)($engines[$table]??''))!=='INNODB')$errors[]='requires_innodb:'.$table;
  return ['ready'=>$errors===[],'errors'=>$errors];
 }
}
