<?php
declare(strict_types=1);

namespace App\Booking\Events;

use App\Central\CentralException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Sends due outbox rows. Each pass takes the oldest pending row of every organisation, so one organisation's events
 * go out in stream order, one at a time. A failed row is retried with backoff and blocks its organisation's later
 * rows until it is published or dead. Dead rows do not block. Run one dispatcher process.
 */
final class BookingEventDispatcher
{
    public const MAX_ATTEMPTS = 10;
    private const BASE_BACKOFF_SECONDS = 30;
    private const MAX_BACKOFF_SECONDS = 3600;

    /** @var \Closure(): DateTimeImmutable */
    private \Closure $clock;

    public function __construct(
        private readonly BookingEventOutboxStore $store,
        private readonly BookingEventTransport $transport,
        private readonly BookingEventEnvelope $envelope,
        ?\Closure $clock = null,
    ) {
        $this->clock = $clock ?? static fn (): DateTimeImmutable => new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }

    /** @return int number of events published in this call */
    public function drain(int $limit = 100): int
    {
        if ($limit < 1) {
            return 0;
        }
        $published = 0;
        $attempted = 0;
        while ($attempted < $limit) {
            $now = ($this->clock)();
            $heads = $this->store->dueHeads($now, $limit - $attempted);
            if ($heads === []) {
                break;
            }
            foreach ($heads as $row) {
                $attempted++;
                $eventId = (string) $row['event_id'];
                try {
                    $body = $this->envelope->body($row);
                    $this->transport->send($body, $eventId);
                } catch (\Throwable $e) {
                    $attempts = (int) $row['attempts'] + 1;
                    $delay = min(self::BASE_BACKOFF_SECONDS * (2 ** min($attempts - 1, 20)), self::MAX_BACKOFF_SECONDS);
                    $this->store->markFailed(
                        $eventId,
                        $attempts,
                        ($this->clock)()->modify('+'.$delay.' seconds'),
                        $this->reasonOf($e),
                        $attempts >= self::MAX_ATTEMPTS,
                    );
                    continue;
                }
                $this->store->markPublished($eventId, ($this->clock)());
                $published++;
            }
        }

        return $published;
    }

    /** Only a fixed code is stored. Messages can carry URLs or bodies, so they are never kept. */
    private function reasonOf(\Throwable $e): string
    {
        if ($e instanceof CentralException) {
            return substr($e->reason, 0, 255);
        }

        return 'transport_failed';
    }
}
