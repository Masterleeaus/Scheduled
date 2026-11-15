<?php
declare(strict_types=1);

namespace App\Central\Events;

/** Process-local store for tests and single-process development only. Not durable and not shared. */
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
