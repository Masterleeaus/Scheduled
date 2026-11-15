<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Required source columns; queried from INFORMATION_SCHEMA by the schema audit. */
final class BookingSchemaColumnContract {
 public static function required():array {return [
  'ct_bookings'=>['id','order_id','client_id','booking_date_time','booking_status','staff_ids','service_id','method_id','method_unit_id','method_unit_qty'],
  'ct_order_client_info'=>['id','order_id','order_duration','client_name','client_email','client_phone'],
  'ct_service_methods_units'=>['id','services_id','methods_id','minlimit','maxlimit','base_price','status'],
  'ct_services_methods_units_rate'=>['id','units_id','rules','rates'],
  'titan_booking_requests'=>['request_key','fingerprint','state','booking_id'],
  'titan_booking_orders'=>['id']
 ];}
 public static function missing(array $actual):array {
  $missing=[];
  foreach(self::required() as $table=>$cols){$existing=$actual[$table]??[];foreach($cols as $col)if(!in_array($col,$existing,true))$missing[]=$table.'.'.$col;}
  return $missing;
 }
}
