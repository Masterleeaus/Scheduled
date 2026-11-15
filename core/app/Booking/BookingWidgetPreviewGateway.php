<?php
namespace App\Booking;
use App\Booking\Widget\WidgetPayload;
use App\Booking\Widget\WidgetPreviewFlag;
/** Only authenticated business-owner requests. Cleanto database identity comes from the server registry. */
final class BookingWidgetPreviewGateway {
 public function __construct(private BookingTenantRegistry $registry,private InternalBookingClient $client) {}
 public function forOwner(int $ownerId,array $input):array {
  $tenant=$this->registry->forOwner($ownerId);
  $data=WidgetPayload::parse($input);
  $data['service']['service_id']=$this->registry->cleantoServiceId($tenant['id'],$data['service']['service_id']);
  return WidgetPreviewFlag::mark($this->client->call($tenant,'booking_preview',$data));
 }
}
