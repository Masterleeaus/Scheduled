<?php
declare(strict_types=1);
namespace App\Booking\Public;
use App\Booking\BookingCatalogueGateway;
use App\Booking\BookingTenantRegistry;
use App\Booking\InternalBookingClient;
final class PublicAvailabilityGateway {
 public function __construct(private PublicDomainRegistry $domains,private BookingCatalogueGateway $catalogue,private BookingTenantRegistry $registry,private InternalBookingClient $client){}
 public function forHost(string $host,array $input):array {
  $request=PublicAvailabilityInput::parse($input);$owner=$this->domains->ownerForHost($host);
  $catalogue=PublicCatalogProjection::fromOwnerCatalogue($this->catalogue->forOwner($owner));
  PublicStaffMembership::assertMember($catalogue,$request['staff_id']);
  $tenant=$this->registry->forOwner($owner);
  return PublicSlotProjection::fromSidecar($this->client->call($tenant,'availability',$request),$request);
 }
}
