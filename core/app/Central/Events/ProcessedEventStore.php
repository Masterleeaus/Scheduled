<?php
declare(strict_types=1);

namespace App\Central\Events;

/**
 * Records which events a consumer has handled. claim() returns false for a duplicate. release() undoes a claim
 * when the effect fails, so a later delivery can retry. A production store must make claim and the effect
 * commit in one database transaction. DatabaseProcessedEventStore claims durably but does not yet wrap the effect.
 */
interface ProcessedEventStore
{
    public function claim(string $consumer, string $idempotencyKey, string $eventId): bool;

    public function release(string $consumer, string $idempotencyKey): void;
}
