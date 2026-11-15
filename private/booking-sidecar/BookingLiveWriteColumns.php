<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Columns directly read or written by the shared transaction path. */
final class BookingLiveWriteColumns {
 public static function required():array {return [
 'ct_bookings'=>['id','order_id','client_id','order_date','booking_date_time','service_id','method_id','method_unit_id','method_unit_qty','method_unit_qty_rate','booking_status','reject_reason','reminder_status','lastmodify','read_status','staff_ids'],
 'ct_order_client_info'=>['id','order_id','client_name','client_email','client_phone','client_personal_info','order_duration','recurring_id'],
 'titan_booking_orders'=>['id'],
 'titan_booking_intent_claims'=>['claim_key','owner_id','fingerprint','state','booking_id'],
 ];}
 public static function missing(array $actual):array {$errors=[];foreach(self::required() as $table=>$columns){foreach($columns as $column){if(!in_array($column,$actual[$table]??[],true))$errors[]=$table.'.'.$column;}}return $errors;}
}
