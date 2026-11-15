<?php
namespace App\Booking\Public;
use App\Booking\BookingCatalogueGateway;
final class PublicCatalogueGateway {
 public function __construct(private PublicDomainRegistry $domains,private BookingCatalogueGateway $catalogue){}
 public function forHost(string $host):array {return PublicCatalogProjection::fromOwnerCatalogue($this->catalogue->forOwner($this->domains->ownerForHost($host)));}
}
