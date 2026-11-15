<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingSchemaInspector {
 public static function expected():array{return [
  'ct_bookings'=>['id','order_id','client_id','booking_date_time','booking_status','staff_ids'],
  'ct_order_client_info'=>['order_id','order_duration'],
  'ct_service_methods_units'=>['id','services_id','methods_id','minlimit','maxlimit','base_price','status'],
  'ct_services_methods_units_rate'=>['units_id','units','rules','rates'],
  'ct_week_days_available'=>['provider_id','week_id','weekday_id','day_start_time','day_end_time']
 ];}
 public static function diff(array $actual):array {$errors=[];foreach(self::expected() as $table=>$columns){foreach($columns as $column)if(!in_array($column,$actual[$table]??[],true))$errors[]=$table.'.'.$column;}return $errors;}
}
