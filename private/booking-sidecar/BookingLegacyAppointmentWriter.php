<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;
/** Uses columns declared in Cleanto class_configure.php q3. */
final class BookingLegacyAppointmentWriter {
 public function __construct(private PDO $db){}
 public function insert(int $orderId,int $clientId,array $cmd,string $rate):int {
  if(BookingLiveWriteIds::order($orderId)<1||BookingLiveWriteIds::foreignId($clientId)<1)throw new \InvalidArgumentException('Invalid owner identifiers');
  $money=BookingMoney::cents($rate);BookingLiveWriteCommandPolicy::assert($cmd);$payload=BookingPayloadMapper::booking($cmd,$orderId,$clientId);
  $s=$this->db->prepare("INSERT INTO ct_bookings (order_id,client_id,order_date,booking_date_time,service_id,method_id,method_unit_id,method_unit_qty,method_unit_qty_rate,booking_status,reject_reason,reminder_status,lastmodify,read_status,staff_ids) VALUES (?,?,?,?,?,?,?,?,?,'A','','0',NOW(),'U',?)");
  if($s===false||$s->execute([$orderId,$clientId,$payload['order_date'],$payload['booking_date_time'],$payload['service_id'],$payload['method_id'],$payload['method_unit_id'],$payload['method_unit_qty'],BookingMoney::decimal($money),$payload['staff_ids']])!==true)throw new \RuntimeException('Appointment insert failed');
  BookingAffectedRows::inserted($s,'Appointment');
  return BookingPositiveId::parse($this->db->lastInsertId());
 }
}
