<?php
declare(strict_types=1);

namespace App\Booking\Events;

use App\Central\CentralException;
use App\Central\Events\CanonicalJson;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Entry point for S4 and S5. Call one of the methods inside the transaction that changes the booking, after the
 * change is written. Calling it outside a transaction is a bug: the outbox row must commit with the change.
 * This class does no network I/O. The dispatcher sends the rows later.
 */
final class BookingEventPublisher
{
    public const MAX_PAYLOAD_BYTES = 4096;
    private const MAX_REVISION = 2147483647;

    /** @var \Closure(): DateTimeImmutable */
    private \Closure $clock;

    /** @var \Closure(): string */
    private \Closure $eventIds;

    public function __construct(
        private readonly BookingEventOutboxStore $store,
        ?\Closure $clock = null,
        ?\Closure $eventIds = null,
    ) {
        $this->clock = $clock ?? static fn (): DateTimeImmutable => new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->eventIds = $eventIds ?? static fn (): string => self::uuidV4();
    }

    /**
     * @param array{bookingId:string,companyId:string,occurredAt:string,serviceId:string,staffIds:list<string>,
     *   startAt:string,endAt:string,timezone:string,status:string,customerRef:string} $booking
     * @param int $revision the booking's own revision number. A new change gets a new revision; a retry does not.
     * @return 'inserted'|'replayed'
     */
    public function created(array $booking, int $revision, ?string $correlationId = null): string
    {
        return $this->record(BookingEventType::CREATED, $booking, $revision, $correlationId);
    }

    /** @return 'inserted'|'replayed' */
    public function cancelled(array $booking, int $revision, ?string $correlationId = null): string
    {
        return $this->record(BookingEventType::CANCELLED, $booking, $revision, $correlationId);
    }

    /** @return 'inserted'|'replayed' */
    public function rescheduled(array $booking, int $revision, ?string $correlationId = null): string
    {
        return $this->record(BookingEventType::RESCHEDULED, $booking, $revision, $correlationId);
    }

    /**
     * @param array<string,mixed> $booking
     * @return 'inserted'|'replayed'
     */
    public function record(string $eventType, array $booking, int $revision, ?string $correlationId = null): string
    {
        if (!in_array($eventType, BookingEventType::ALL, true)) {
            throw new CentralException('unknown_event_type', 'Booking event type is not recognised.');
        }
        if ($revision < 1 || $revision > self::MAX_REVISION) {
            throw new CentralException('invalid_field', 'revision must be a positive integer.');
        }
        $payload = BookingEventFacts::payload($booking);
        $canonical = CanonicalJson::encode($payload);
        if (strlen($canonical) > self::MAX_PAYLOAD_BYTES) {
            throw new CentralException('payload_too_large', 'Booking event payload exceeds the size limit.');
        }

        $key = 'booking:'.$payload['bookingId'].':'.$eventType.':r'.$revision;
        $correlation = $correlationId ?? 'booking:'.$payload['bookingId'];
        if (strlen($correlation) < 1 || strlen($correlation) > 128) {
            throw new CentralException('invalid_field', 'correlationId must be 1 to 128 characters.');
        }
        $digest = hash('sha256', $canonical);

        $existing = $this->store->find($key);
        if ($existing !== null) {
            return $this->replayOrConflict($existing, $payload['companyId'], $eventType, $digest);
        }

        $now = ($this->clock)()->setTimezone(new DateTimeZone('UTC'));
        $row = [
            'event_id' => ($this->eventIds)(),
            'organisation_id' => $payload['companyId'],
            'event_type' => $eventType,
            'schema_version' => BookingEventType::SCHEMA_VERSION,
            'correlation_id' => $correlation,
            'idempotency_key' => $key,
            // Stored in the column's own DATETIME(3) text form. The envelope converts it to the wire form.
            'occurred_at' => $now->format('Y-m-d H:i:s.v'),
            'payload_json' => $canonical,
            'payload_digest' => $digest,
            'status' => 'pending',
            'attempts' => 0,
            'next_attempt_at' => $now->format('Y-m-d H:i:s.v'),
        ];

        try {
            $this->store->insert($row);
        } catch (\Throwable $e) {
            if (!self::isDuplicateKey($e)) {
                throw $e;
            }
            // A concurrent publish with the same key committed between our find() and insert(). Only this key is
            // handled here: if no row exists for it, the duplicate was on something else and the error is rethrown.
            $winner = $this->store->find($key);
            if ($winner === null) {
                throw $e;
            }

            return $this->replayOrConflict($winner, $payload['companyId'], $eventType, $digest);
        }

        return 'inserted';
    }

    /**
     * @param array<string,mixed> $existing
     * @return 'replayed'
     */
    private function replayOrConflict(array $existing, string $companyId, string $eventType, string $digest): string
    {
        if (($existing['organisation_id'] ?? null) === $companyId
            && ($existing['event_type'] ?? null) === $eventType
            && ($existing['payload_digest'] ?? null) === $digest) {
            return 'replayed';
        }

        throw new CentralException('idempotency_conflict', 'Idempotency key was used for different data.');
    }

    /** 23000 is MySQL and SQLite; 23505 is PostgreSQL. Both mean a unique key already exists. */
    private static function isDuplicateKey(\Throwable $e): bool
    {
        return $e instanceof \PDOException && in_array((string) $e->getCode(), ['23000', '23505'], true);
    }

    private static function uuidV4(): string
    {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        $h = bin2hex($b);

        return substr($h, 0, 8).'-'.substr($h, 8, 4).'-'.substr($h, 12, 4).'-'.substr($h, 16, 4).'-'.substr($h, 20, 12);
    }
}
