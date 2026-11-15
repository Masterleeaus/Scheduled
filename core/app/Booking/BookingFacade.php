<?php

namespace App\Booking;

/** Composition root for authenticated tenant-bound booking operations. */
final class BookingFacade
{
    public function __construct(private BookingTenantRegistry $registry, private InternalBookingClient $client) {}

    public function slots(int $authenticatedBusinessOwnerId, array $input): array
    {
        $tenant = $this->registry->forOwner($authenticatedBusinessOwnerId);
        $normalized = BookingPayload::slots($input);
        $normalized['service_id'] = $this->registry->cleantoServiceId($tenant['id'], $normalized['service_id']);
        return $this->client->call($tenant, 'slots', $normalized);
    }
}
