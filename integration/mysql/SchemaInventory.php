<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class SchemaInventory {
 public static function required():array{return ['ct_bookings'=>['id','order_id','client_id','booking_date_time','staff_ids','booking_status'], 'ct_order_client_info'=>['order_id','order_duration'], 'ct_services'=>['id'], 'ct_week_days_available'=>['provider_id'], 'titan_booking_requests'=>['request_key','fingerprint','state','booking_id']];}
 public static function missing(array $actual):array {$missing=[];foreach(self::required() as $table=>$fields)foreach($fields as $field)if(!in_array($field,$actual[$table]??[],true))$missing[]=$table.'.'.$field;return $missing;}
}
