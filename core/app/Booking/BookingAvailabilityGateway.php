<?php
namespace App\Booking;
/** Internal only until Laravel can enforce tenant-owned staff mapping. */
final class BookingAvailabilityGateway {
 public function __construct(private BookingTenantRegistry $registry,private InternalBookingClient $client) {}
 public function forOwner(int $owner,array $input): array {
  $tenant=$this->registry->forOwner($owner);
  return $this->client->call($tenant,'availability',BookingAvailabilityRequest::normalize($input));
 }
}
