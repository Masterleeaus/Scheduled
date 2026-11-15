<?php
namespace App\Booking;
use App\Booking\Widget\CatalogSnapshot;
/** Server-side owner catalogue. Native migration is explicitly opted in per tenant.
 * Existing signed sidecar remains the default until native MySQL acceptance succeeds.
 */
final class BookingCatalogueGateway {
 public function __construct(private BookingTenantRegistry $registry,private InternalBookingClient $client,private NativeCleantoCatalogue $native) {}
 public function forOwner(int $ownerId):array {
  $tenant=$this->registry->forOwner($ownerId);
  $bindings=config('booking_internal.native_catalogue_tenants',[]);
  $binding=is_array($bindings)?($bindings[$ownerId]??null):null;
  if(is_array($binding) && ($binding['enabled']??false)===true){
   // A configured native failure must fail closed, never silently read a different tenant.
   $snapshot=$this->native->read($tenant,$binding);
  }else{
   $data=$this->client->call($tenant,'booking_catalogue',[]);
   $snapshot=CatalogSnapshot::build($data);
  }
  return ['services'=>$snapshot['services'],'methods'=>$snapshot['methods'],'units'=>$snapshot['units'],'staff'=>$snapshot['staff'],'preview_only'=>true,'booking_writes_enabled'=>false];
 }
}
