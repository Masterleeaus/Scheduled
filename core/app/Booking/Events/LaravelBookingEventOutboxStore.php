<?php
declare(strict_types=1);

namespace App\Booking\Events;

use App\Central\CentralException;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

/**
 * Database store for the booking outbox. insert() must run inside a DB transaction, because it takes the
 * organisation's stream row lock (SELECT ... FOR UPDATE) and that lock lasts until the caller commits. Without the
 * lock, two concurrent writers could commit out of stream order.
 */
final class LaravelBookingEventOutboxStore implements BookingEventOutboxStore
{
    private const OUTBOX = 'booking_event_outbox';
    private const STREAMS = 'booking_event_streams';

    public function __construct(private readonly ?string $connection = null)
    {
    }

    public function find(string $idempotencyKey): ?array
    {
        $row = $this->db()->table(self::OUTBOX)->where('idempotency_key', $idempotencyKey)->first();

        return $row === null ? null : (array) $row;
    }

    public function insert(array $row): void
    {
        $db = $this->db();
        if ($db->transactionLevel() < 1) {
            throw new CentralException('no_transaction', 'Booking event rows must be written inside a transaction.');
        }
        $org = (string) $row['organisation_id'];
        $db->table(self::STREAMS)->insertOrIgnore(['organisation_id' => $org, 'last_seq' => 0]);
        $stream = $db->table(self::STREAMS)->where('organisation_id', $org)->lockForUpdate()->first();
        $seq = (int) $stream->last_seq + 1;
        $db->table(self::STREAMS)->where('organisation_id', $org)->update(['last_seq' => $seq]);
        $db->table(self::OUTBOX)->insert($row + ['stream_seq' => $seq]);
    }

    public function dueHeads(DateTimeImmutable $now, int $limit): array
    {
        $heads = $this->db()->table(self::OUTBOX.' as o')
            ->joinSub(
                $this->db()->table(self::OUTBOX)
                    ->selectRaw('organisation_id, MIN(stream_seq) as head_seq')
                    ->where('status', 'pending')
                    ->groupBy('organisation_id'),
                'h',
                static fn ($join) => $join->on('o.organisation_id', '=', 'h.organisation_id')
                    ->on('o.stream_seq', '=', 'h.head_seq'),
            )
            ->where('o.status', 'pending')
            ->where('o.next_attempt_at', '<=', $this->utc($now))
            ->orderBy('o.id')
            ->limit($limit)
            ->select('o.*')
            ->get();

        return array_map(static fn ($row): array => (array) $row, $heads->all());
    }

    public function markPublished(string $eventId, DateTimeImmutable $at): void
    {
        $this->db()->table(self::OUTBOX)->where('event_id', $eventId)->update([
            'status' => 'published',
            'published_at' => $this->utc($at),
            'last_error' => null,
        ]);
    }

    public function markFailed(string $eventId, int $attempts, DateTimeImmutable $nextAttemptAt, string $error, bool $dead): void
    {
        $this->db()->table(self::OUTBOX)->where('event_id', $eventId)->update([
            'status' => $dead ? 'dead' : 'pending',
            'attempts' => $attempts,
            'next_attempt_at' => $this->utc($nextAttemptAt),
            'last_error' => substr($error, 0, 255),
        ]);
    }

    private function db(): ConnectionInterface
    {
        return DB::connection($this->connection);
    }

    private function utc(DateTimeImmutable $at): string
    {
        return $at->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.v');
    }
}
