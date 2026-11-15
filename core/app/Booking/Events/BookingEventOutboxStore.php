<?php
declare(strict_types=1);

namespace App\Booking\Events;

use DateTimeImmutable;

/**
 * Storage for the booking event outbox. `insert` must run on the caller's transaction handle, so the row commits or
 * rolls back with the booking change. Rows are ordered per organisation by stream_seq.
 */
interface BookingEventOutboxStore
{
    /** @return array<string,mixed>|null the existing row for this idempotency key */
    public function find(string $idempotencyKey): ?array;

    /**
     * Inserts the row and assigns its stream_seq (next value for the organisation). The caller's transaction must
     * hold the organisation's stream lock until it commits.
     *
     * @param array<string,mixed> $row
     */
    public function insert(array $row): void;

    /**
     * The oldest pending row of each organisation, only when it is due. A later pending row of an organisation is
     * never returned while an earlier one is pending. Dead rows are ignored.
     *
     * @return list<array<string,mixed>>
     */
    public function dueHeads(DateTimeImmutable $now, int $limit): array;

    public function markPublished(string $eventId, DateTimeImmutable $at): void;

    public function markFailed(string $eventId, int $attempts, DateTimeImmutable $nextAttemptAt, string $error, bool $dead): void;
}
