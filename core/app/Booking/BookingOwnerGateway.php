<?php
namespace App\Booking;
/** Tenant derived only from authenticated PlusAgency owner. */
final class BookingOwnerGateway {
 public function __construct(private BookingTenantRegistry $registry,private InternalBookingClient $client){}
 public function call(int $ownerId,string $operation,array $data):array {
  if(!in_array($operation,['owner_booking_list','owner_booking_get','owner_booking_create','owner_booking_cancel','owner_booking_reschedule','owner_booking_summary'],true))throw new \InvalidArgumentException('Unsupported operation');
  $tenant=$this->registry->forOwner($ownerId);
  unset($data['tenant_id'],$data['instance_key'],$data['database_reference'],$data['actor']);
  if($operation==='owner_booking_create'){
   if(!isset($data['service']['service_id']))throw new \InvalidArgumentException('Service required');
   $data['service']['service_id']=$this->registry->cleantoServiceId($tenant['id'],(int)$data['service']['service_id']);
  }
  return $this->client->call($tenant,$operation,$data);
 }
}
