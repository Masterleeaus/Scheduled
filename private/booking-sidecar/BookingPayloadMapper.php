<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Maps normalized booking command to Cleanto's legacy column vocabulary. Not a persistence adapter. */
final class BookingPayloadMapper {
 public static function booking(array $cmd,int $orderId,int $clientId):array {
  return ['order_id'=>$orderId,'client_id'=>$clientId,'order_date'=>substr($cmd['window']['local_start'],0,10),
   'booking_date_time'=>$cmd['window']['local_start'],'service_id'=>$cmd['service']['service_id'],
   'method_id'=>$cmd['service']['method_id'],'method_unit_id'=>$cmd['service']['method_unit_id'],
   'method_unit_qty'=>$cmd['service']['quantity'],'booking_status'=>'A','staff_ids'=>BookingStaff::toLegacyCsv($cmd['staff_ids'])];
 }
}
