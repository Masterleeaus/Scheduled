<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use PDO;
/** Writes existing Cleanto order/client table without replacing Cleanto booking logic. */
final class BookingLegacyClientWriter {
 public function __construct(private PDO $db){}
 public function insert(int $orderId,array $customer,int $duration,array $personalInfo=[]):int {
  if(BookingLiveWriteIds::order($orderId)<1||$duration<15||$duration>720)throw new \InvalidArgumentException('Invalid order');
  $c=BookingCustomer::parse($customer);
  $encodedInfo=BookingPersonalInfo::encode($personalInfo);
  BookingLiveWriteCustomer::assert($c);
  $s=$this->db->prepare('INSERT INTO ct_order_client_info (order_id, client_name, client_email, client_phone, client_personal_info, order_duration, recurring_id) VALUES (?,?,?,?,?,?,0)');
  if($s===false||$s->execute([$orderId,$c['name'],$c['email'],$c['phone'],$encodedInfo,$duration])!==true)throw new \RuntimeException('Customer order insert failed');
  BookingAffectedRows::inserted($s,'Customer order');
  return BookingPositiveId::parse($this->db->lastInsertId());
 }
}
