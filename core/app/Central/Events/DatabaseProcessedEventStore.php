<?php
declare(strict_types=1);

namespace App\Central\Events;

/**
 * Durable ProcessedEventStore backed by the central_processed_events table (unique on consumer + idempotency key).
 * claimAndRun() inserts the claim row and runs the effect inside one transaction, so a failed effect rolls the
 * claim back with it and no release() is needed. The effect must write through the same PDO connection to be
 * covered by that transaction. claim() returns false only for a duplicate key; release() deletes the row.
 */
final class DatabaseProcessedEventStore implements ProcessedEventStore
{
    public const TABLE = 'central_processed_events';

    /** MySQL ER_DUP_ENTRY, and SQLite SQLITE_CONSTRAINT_UNIQUE (2067) and SQLITE_CONSTRAINT_PRIMARYKEY (1555). */
    private const DUPLICATE_DRIVER_CODES = [1062, 2067, 1555];

    /** SQLite reports the primary code 19 for every constraint unless extended codes are on. */
    private const SQLITE_CONSTRAINT = 19;

    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function claimAndRun(string $consumer, string $idempotencyKey, string $eventId, callable $effect): bool
    {
        $this->pdo->beginTransaction();
        try {
            if (!$this->claim($consumer, $idempotencyKey, $eventId)) {
                $this->pdo->rollBack();

                return false;
            }
            $effect();
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }

        return true;
    }

    public function claim(string $consumer, string $idempotencyKey, string $eventId): bool
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO '.self::TABLE.' (consumer, idempotency_key, event_id, created_at) VALUES (?, ?, ?, ?)'
        );
        try {
            $statement->execute([$consumer, $idempotencyKey, $eventId, gmdate('Y-m-d H:i:s')]);
        } catch (\PDOException $e) {
            if ($this->isDuplicateKey($e, $consumer, $idempotencyKey)) {
                return false;
            }

            throw $e;
        }

        return true;
    }

    /**
     * Only a unique-key violation for this consumer and key counts as a duplicate. Other integrity failures
     * (NOT NULL, CHECK, foreign key) share SQLSTATE 23000 and must surface as errors.
     */
    private function isDuplicateKey(\PDOException $e, string $consumer, string $idempotencyKey): bool
    {
        $sqlState = (string) $e->getCode();
        if ($sqlState === '23505') {
            return true; // PostgreSQL unique_violation
        }
        if ($sqlState !== '23000') {
            return false;
        }
        $driverCode = (int) ($e->errorInfo[1] ?? 0);
        if (in_array($driverCode, self::DUPLICATE_DRIVER_CODES, true)) {
            return true;
        }
        if ($driverCode !== self::SQLITE_CONSTRAINT) {
            return false;
        }

        // Generic SQLite constraint code: the key is a duplicate only if its claim row already exists.
        $statement = $this->pdo->prepare(
            'SELECT 1 FROM '.self::TABLE.' WHERE consumer = ? AND idempotency_key = ? LIMIT 1'
        );
        $statement->execute([$consumer, $idempotencyKey]);

        return $statement->fetchColumn() !== false;
    }

    public function release(string $consumer, string $idempotencyKey): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM '.self::TABLE.' WHERE consumer = ? AND idempotency_key = ?'
        );
        $statement->execute([$consumer, $idempotencyKey]);
    }
}
