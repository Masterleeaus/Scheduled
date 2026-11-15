<?php
declare(strict_types=1);

namespace App\Booking\Events;

/** Sends one signed envelope body. Implementations throw on any failure; they never return a partial success. */
interface BookingEventTransport
{
    public function send(string $body, string $eventId): void;
}
