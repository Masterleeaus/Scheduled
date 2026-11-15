<?php
namespace App\Booking;
/** Internal-facing reservation diagnostics; deliberately NOT wired to any public route. */
final class BookingConflictGateway {
    public function __construct(private BookingTenantRegistry $registry, private InternalBookingClient $client) {}
    public function forOwner(int $owner, array $payload): array {
        $tenant=$this->registry->forOwner($owner);
        // Staff IDs must eventually be resolved through a tenant-scoped mapping.
        // Until then this method is internal-only, not a frontend or customer endpoint.
        return $this->client->call($tenant, 'busy_intervals', BookingBusyRequest::normalize($payload));
    }
}
