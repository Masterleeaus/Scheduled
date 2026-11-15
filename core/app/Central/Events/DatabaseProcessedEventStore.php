<?php
declare(strict_types=1);

namespace App\Central\Events;

/**
 * Durable ProcessedEventStore backed by the central_processed_events table (unique on consumer + idempotency key).
 * claim() inserts a row and returns false when the unique key already exists. release() deletes the row.
 *
 * Known gap: claim() commits on its own, so a crash between claim and the handler's effect leaves the event
 * claimed and unprocessed. The handler-plus-claim transaction the interface comment asks for is still open.
 */
final class DatabaseProcessedEventStore implements ProcessedEventStore
{
    public const TABLE = 'central_processed_events';

    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function claim(string $consumer, string $idempotencyKey, string $eventId): bool
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO '.self::TABLE.' (consumer, idempotency_key, event_id, created_at) VALUES (?, ?, ?, ?)'
        );
        try {
            $statement->execute([$consumer, $idempotencyKey, $eventId, gmdate('Y-m-d H:i:s')]);
        } catch (\PDOException $e) {
            // 23000 is MySQL and SQLite; 23505 is PostgreSQL. Both mean the unique key already exists.
            if (in_array((string) $e->getCode(), ['23000', '23505'], true)) {
                return false;
            }

            throw $e;
        }

        return true;
    }

    public function release(string $consumer, string $idempotencyKey): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM '.self::TABLE.' WHERE consumer = ? AND idempotency_key = ?'
        );
        $statement->execute([$consumer, $idempotencyKey]);
    }
}
