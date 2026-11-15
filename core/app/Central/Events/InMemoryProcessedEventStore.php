<?php
declare(strict_types=1);

namespace App\Central\Events;

/**
 * Process-local store for tests and single-process development only. Not durable and not shared.
 *
 * SINGLE-INSTANCE ONLY. Under PHP-FPM every request is a new process, so claims do not survive into the next
 * request and duplicates are not detected across requests. Do not use this store for production event
 * handling. See core/config/central.php.
 */
final class InMemoryProcessedEventStore implements ProcessedEventStore
{
    /** @var array<string,string> */
    private array $claims = [];

    public function claim(string $consumer, string $idempotencyKey, string $eventId): bool
    {
        $key = $consumer."\0".$idempotencyKey;
        if (isset($this->claims[$key])) {
            return false;
        }
        $this->claims[$key] = $eventId;

        return true;
    }

    public function release(string $consumer, string $idempotencyKey): void
    {
        unset($this->claims[$consumer."\0".$idempotencyKey]);
    }
}
