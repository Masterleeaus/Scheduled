<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Auditable inventory of existing Cleanto tables needed for booking writes. */
final class BookingLegacySqlContract {
 public static function requiredTables():array {return ['ct_bookings','ct_order_client_info','ct_services_methods_units_rate','ct_services','ct_week_days_available','ct_schedule_breaks','ct_schedule_offtimes','ct_off_days'];}
 public static function requiredColumns():array {return [
 'ct_bookings'=>['order_id','client_id','booking_date_time','booking_status','staff_ids','method_unit_qty_rate'],
 'ct_order_client_info'=>['order_id','order_duration','client_email'],
 'ct_services_methods_units_rate'=>['units_id','rules','rates']
 ];}
 public static function validate(array $tables):array {
  $missing=[];foreach(self::requiredColumns() as $table=>$columns){foreach($columns as $column)if(!in_array($column,$tables[$table]??[],true))$missing[]=$table.'.'.$column;}return $missing;
 }
}
