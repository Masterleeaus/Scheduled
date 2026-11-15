<?php
namespace App\Booking;
/** Only server-side business owner context may select booking instance. */
final class BookingHealth {
    public function __construct(private BookingTenantRegistry $registry, private InternalBookingClient $client) {}
    public function services(int $ownerId): array {
        return $this->client->call($this->registry->forOwner($ownerId), 'services', []);
    }
    public function health(int $ownerId): array {
        return $this->client->call($this->registry->forOwner($ownerId), 'health', []);
    }
}
