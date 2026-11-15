<?php
declare(strict_types=1);

namespace App\Booking\Events;

/** Event names and versions sent from Scheduled to Central. Names are fixed by Central's registry (C5). */
final class BookingEventType
{
    public const SOURCE_PRODUCT = 'scheduled';
    public const SCHEMA_VERSION = 1;
    /*
     * Wire names. Central's eventType rule (EVENTS.md, ^[a-z][a-z0-9_]{0,63}$) forbids dots, so the envelope carries
     * these. The prose names are booking.created, booking.cancelled and booking.rescheduled.
     */
    public const CREATED = 'booking_created';
    public const CANCELLED = 'booking_cancelled';
    public const RESCHEDULED = 'booking_rescheduled';

    /** @var list<string> */
    public const ALL = [self::CREATED, self::CANCELLED, self::RESCHEDULED];

    private function __construct()
    {
    }
}
