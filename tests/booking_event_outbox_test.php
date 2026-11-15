<?php
declare(strict_types=1);

/*
 * Booking event outbox checks. Included from tests/run.php, which defines check() and reasonOf().
 * No network and no database: the store and the transport are in-memory doubles.
 */

use App\Booking\Events\BookingEventDispatcher;
use App\Booking\Events\BookingEventEnvelope;
use App\Booking\Events\BookingEventOutboxStore;
use App\Booking\Events\BookingEventPublisher;
use App\Booking\Events\BookingEventTransport;
use App\Central\CentralException;
use App\Central\Events\CanonicalJson;
use App\Central\Events\SignedEventVerifier;

final class BookingOutboxTestClock
{
    public function __construct(public DateTimeImmutable $now)
    {
    }

    public function closure(): \Closure
    {
        return fn (): DateTimeImmutable => $this->now;
    }
}

final class InMemoryBookingOutbox implements BookingEventOutboxStore
{
    /** @var array<string,array<string,mixed>> keyed by event_id */
    public array $rows = [];
    private int $autoId = 0;

    public function find(string $idempotencyKey): ?array
    {
        foreach ($this->rows as $row) {
            if ($row['idempotency_key'] === $idempotencyKey) {
                return $row;
            }
        }

        return null;
    }

    public function insert(array $row): void
    {
        $org = $row['organisation_id'];
        $next = 1 + max([0, ...array_map(
            static fn (array $r): int => $r['organisation_id'] === $org ? (int) $r['stream_seq'] : 0,
            $this->rows,
        )]);
        $row['id'] = ++$this->autoId;
        $row['stream_seq'] = $next;
        $this->rows[$row['event_id']] = $row;
    }

    public function dueHeads(DateTimeImmutable $now, int $limit): array
    {
        $heads = [];
        foreach ($this->rows as $row) {
            if ($row['status'] !== 'pending') {
                continue;
            }
            $org = $row['organisation_id'];
            if (!isset($heads[$org]) || $row['stream_seq'] < $heads[$org]['stream_seq']) {
                $heads[$org] = $row;
            }
        }
        $due = array_filter(
            $heads,
            static fn (array $r): bool => $r['next_attempt_at'] <= $now->format('Y-m-d H:i:s.v'),
        );
        usort($due, static fn (array $a, array $b): int => $a['id'] <=> $b['id']);

        return array_slice(array_values($due), 0, $limit);
    }

    public function markPublished(string $eventId, DateTimeImmutable $at): void
    {
        $this->rows[$eventId]['status'] = 'published';
        $this->rows[$eventId]['published_at'] = $at->format('Y-m-d H:i:s.v');
        $this->rows[$eventId]['last_error'] = null;
    }

    public function markFailed(string $eventId, int $attempts, DateTimeImmutable $nextAttemptAt, string $error, bool $dead): void
    {
        $this->rows[$eventId]['status'] = $dead ? 'dead' : 'pending';
        $this->rows[$eventId]['attempts'] = $attempts;
        $this->rows[$eventId]['next_attempt_at'] = $nextAttemptAt->format('Y-m-d H:i:s.v');
        $this->rows[$eventId]['last_error'] = $error;
    }

    /** @return list<array<string,mixed>> rows in insertion order */
    public function ordered(): array
    {
        $rows = array_values($this->rows);
        usort($rows, static fn (array $a, array $b): int => $a['id'] <=> $b['id']);

        return $rows;
    }
}

final class RecordingBookingTransport implements BookingEventTransport
{
    /** @var list<array{eventId:string,body:string}> */
    public array $sent = [];

    /** @param (\Closure(string,string):bool)|null $fail receives (eventType, organisationId) */
    public function __construct(private ?\Closure $fail = null)
    {
    }

    public function send(string $body, string $eventId): void
    {
        $decoded = json_decode($body, true, 32, JSON_THROW_ON_ERROR);
        if ($this->fail !== null && ($this->fail)($decoded['eventType'], $decoded['organisationId'])) {
            // The message carries a URL on purpose: the dispatcher must not store it.
            throw new CentralException('transport_failed', 'simulated failure at https://consumer.invalid/x?token=abc');
        }
        $this->sent[] = ['eventId' => $eventId, 'body' => $body];
    }
}

function runBookingOutboxChecks(): void
{
    $secret = str_repeat('s3cr3t-Booking-Events-', 2);
    $companyA = '6f1c2b1e-8a1d-4c2e-9b1a-1234567890ab';
    $companyB = '9a0b7c6d-5e4f-4a3b-8c2d-0f1e2d3c4b5a';
    $booking = [
        'bookingId' => 'bk1001',
        'companyId' => $companyA,
        'occurredAt' => '2026-11-15T09:00:00Z',
        'serviceId' => 'svc-clean-std',
        'staffIds' => ['st-2', 'st-1', 'st-2'],
        'startAt' => '2026-11-20T10:00:00+11:00',
        'endAt' => '2026-11-20T12:00:00+11:00',
        'timezone' => 'Australia/Melbourne',
        'status' => 'confirmed',
        'customerRef' => 'cust_7f3a9c21',
    ];
    $t0 = new DateTimeImmutable('2026-11-15T09:00:05+00:00');
    $nowSec = $t0->getTimestamp();

    // --- Canonical JSON -------------------------------------------------------
    check(
        CanonicalJson::encode(['b' => 1, 'a' => ['d' => true, 'c' => null], 'list' => [3, 1, 2]])
            === '{"a":{"c":null,"d":true},"b":1,"list":[3,1,2]}',
        'canonical: keys sorted at every depth, lists keep order, no whitespace',
    );
    check(CanonicalJson::encode(['u' => 'a/é']) === '{"u":"a/é"}', 'canonical: slashes and unicode left unescaped');
    check(reasonOf(fn () => CanonicalJson::encode(['x' => NAN])) === 'invalid_payload', 'canonical: NaN refused');

    // --- Payload facts --------------------------------------------------------
    $payload = App\Booking\Events\BookingEventFacts::payload($booking);
    check($payload['startAt'] === '2026-11-19T23:00:00.000Z', 'facts: start normalised to UTC milliseconds');
    check($payload['staffIds'] === ['st-1', 'st-2'], 'facts: staffIds deduplicated and sorted');
    check(count($payload) === 10, 'facts: exactly the ten approved fields');
    check(reasonOf(fn () => App\Booking\Events\BookingEventFacts::payload($booking + ['customerEmail' => 'a@b.co'])) === 'invalid_payload', 'facts: unknown field refused');
    $missing = $booking;
    unset($missing['status']);
    check(reasonOf(fn () => App\Booking\Events\BookingEventFacts::payload($missing)) === 'invalid_payload', 'facts: missing field refused');
    check(reasonOf(fn () => App\Booking\Events\BookingEventFacts::payload(['customerRef' => 'jo@example.com'] + $booking)) === 'invalid_field', 'facts: email-shaped customerRef refused');
    check(reasonOf(fn () => App\Booking\Events\BookingEventFacts::payload(['endAt' => '2026-11-20T09:00:00+11:00'] + $booking)) === 'invalid_field', 'facts: endAt not after startAt refused');
    check(reasonOf(fn () => App\Booking\Events\BookingEventFacts::payload(['timezone' => 'Mars/Olympus'] + $booking)) === 'invalid_field', 'facts: unknown timezone refused');
    check(reasonOf(fn () => App\Booking\Events\BookingEventFacts::payload(['companyId' => 'not-a-uuid'] + $booking)) === 'invalid_field', 'facts: non-UUID companyId refused');
    $manyStaff = array_map(static fn (int $i): string => 'st-'.$i, range(1, 21));
    check(reasonOf(fn () => App\Booking\Events\BookingEventFacts::payload(['staffIds' => $manyStaff] + $booking)) === 'invalid_field', 'facts: more than 20 staff refused');

    // --- Publisher and store --------------------------------------------------
    $clock = new BookingOutboxTestClock($t0);
    $store = new InMemoryBookingOutbox();
    $publisher = new BookingEventPublisher($store, $clock->closure(), static fn (): string => '11111111-2222-4333-8444-555555555555');

    check($publisher->created($booking, 1) === 'inserted', 'publisher: first created call inserts');
    $row = $store->ordered()[0] ?? [];
    check(($row['event_type'] ?? '') === 'booking_created', 'publisher: wire type is booking_created');
    check(($row['idempotency_key'] ?? '') === 'booking:bk1001:booking_created:r1', 'publisher: idempotency key is booking, type and revision');
    check(($row['correlation_id'] ?? '') === 'booking:bk1001', 'publisher: correlation defaults to the booking');
    check(($row['occurred_at'] ?? '') === '2026-11-15 09:00:05.000' && ($row['status'] ?? '') === 'pending', 'publisher: row time is the DATETIME(3) text form, UTC, status pending');
    check($publisher->created($booking, 1) === 'replayed' && count($store->rows) === 1, 'publisher: same key and data is a replay');
    check(reasonOf(fn () => $publisher->created(['status' => 'pending'] + $booking, 1)) === 'idempotency_conflict', 'publisher: same key with different data is refused');
    check(reasonOf(fn () => $publisher->created($booking, 0)) === 'invalid_field', 'publisher: revision 0 refused');
    check(reasonOf(fn () => $publisher->record('booking.created', $booking, 2)) === 'unknown_event_type', 'publisher: dotted name is not a wire type');

    // --- Envelope and signature (checked with Central's own verifier) ---------
    $envelope = new BookingEventEnvelope($secret);
    $body = $envelope->body($row);
    $verified = (new SignedEventVerifier($secret))->verify($body, $nowSec);
    check($verified['sourceProduct'] === 'scheduled' && $verified['organisationId'] === $companyA, 'envelope: source product and tenant');
    check($verified['eventType'] === 'booking_created' && $verified['schemaVersion'] === 1, 'envelope: type and schema version 1');
    check($verified['occurredAt'] === '2026-11-15T09:00:05.000Z' && $verified['idempotencyKey'] === 'booking:bk1001:booking_created:r1', 'envelope: occurredAt and idempotency key');
    check(count($verified['payload']) === 10 && $verified['payload']['bookingId'] === 'bk1001', 'envelope: payload carried intact');
    check(strpos($body, 'cust_7f3a9c21') !== false && strpos($body, 'Booking-Events') === false, 'envelope: opaque customer ref only, secret not in body');
    check(reasonOf(fn () => (new SignedEventVerifier(str_repeat('x', 40)))->verify($body, $nowSec)) === 'bad_signature', 'signing: wrong secret does not verify');
    $tampered = str_replace('"status":"confirmed"', '"status":"cancelled"', $body);
    check(reasonOf(fn () => (new SignedEventVerifier($secret))->verify($tampered, $nowSec)) === 'bad_signature', 'signing: tampered payload does not verify');
    check(
        SignedEventVerifier::canonicalJson(json_decode('{"b":[2,1],"a":"x/y"}', false)) === CanonicalJson::encode(['a' => 'x/y', 'b' => [2, 1]]),
        'signing: canonical form matches Central verifier',
    );
    check(reasonOf(fn () => new BookingEventEnvelope('too-short')) === 'config_invalid', 'signing: short secret refused');

    // occurredAt round trip: the row's DATETIME(3) text is rewritten to the wire form before signing, so the string
    // that is signed is the string that is sent. The body is serialised, decoded and verified, as a consumer would.
    $wireAt = '2026-11-15T09:00:05.000Z';
    $sentBody = $envelope->body($row);
    $sentDecoded = json_decode($sentBody, true, 32, JSON_THROW_ON_ERROR);
    check(($sentDecoded['occurredAt'] ?? null) === $wireAt, 'roundtrip: sent occurredAt is the ISO millisecond form');
    check(strpos($sentBody, '"occurredAt":"'.$wireAt.'"') !== false, 'roundtrip: serialised body carries the exact wire string');
    check((new SignedEventVerifier($secret))->verify($sentBody, $nowSec)['occurredAt'] === $wireAt, 'roundtrip: signature covers the sent occurredAt');
    $fromDb = json_decode(json_encode($row, JSON_THROW_ON_ERROR), true, 32, JSON_THROW_ON_ERROR);
    check($envelope->body($fromDb) === $sentBody, 'roundtrip: a row re-read from its serialised form gives the same body');
    check(BookingEventEnvelope::wireTime('2026-11-15T09:00:05.000Z') === $wireAt, 'roundtrip: ISO input normalises to the same wire string');
    check(BookingEventEnvelope::wireTime('2026-11-15 09:00:05.120') === '2026-11-15T09:00:05.120Z', 'roundtrip: milliseconds are kept');
    check(reasonOf(fn () => BookingEventEnvelope::wireTime('2026-11-15 09:00:05')) === 'invalid_payload', 'roundtrip: text without milliseconds refused');
    check(reasonOf(fn () => BookingEventEnvelope::wireTime('2026-11-15T09:00:05.000+11:00')) === 'invalid_payload', 'roundtrip: non-UTC offset refused');
    $offForm = $row;
    $offForm['occurred_at'] = '2026-11-15T09:00:05.000+11:00';
    check(reasonOf(fn () => $envelope->body($offForm)) === 'invalid_payload', 'roundtrip: a stored offset form is refused, not signed');
    $badDigest = $row;
    $badDigest['payload_json'] = str_replace('"bk1001"', '"bk9999"', $row['payload_json']);
    check(reasonOf(fn () => $envelope->body($badDigest)) === 'invalid_payload', 'signing: payload that does not match its digest is refused');

    // --- Ordering and delivery ------------------------------------------------
    $store2 = new InMemoryBookingOutbox();
    $clock2 = new BookingOutboxTestClock($t0);
    $pub2 = new BookingEventPublisher($store2, $clock2->closure());
    $pub2->created($booking, 1);
    $pub2->rescheduled(['startAt' => '2026-11-21T10:00:00+11:00', 'endAt' => '2026-11-21T12:00:00+11:00'] + $booking, 2);
    $pub2->cancelled(['status' => 'cancelled'] + $booking, 3);
    $pub2->created(['bookingId' => 'bk9', 'companyId' => $companyB] + $booking, 1);

    $seqs = array_map(static fn (array $r): array => [$r['organisation_id'], $r['stream_seq']], $store2->ordered());
    check($seqs === [[$companyA, 1], [$companyA, 2], [$companyA, 3], [$companyB, 1]], 'ordering: stream sequence counts per tenant');

    $transport = new RecordingBookingTransport();
    $dispatcher = new BookingEventDispatcher($store2, $transport, $envelope, $clock2->closure());
    check($dispatcher->drain(10) === 4, 'delivery: all four events published when nothing fails');
    $typesA = [];
    foreach ($transport->sent as $sent) {
        $decoded = (new SignedEventVerifier($secret))->verify($sent['body'], $nowSec);
        if ($decoded['organisationId'] === $companyA) {
            $typesA[] = $decoded['eventType'];
        }
    }
    check($typesA === ['booking_created', 'booking_rescheduled', 'booking_cancelled'], 'ordering: one tenant goes out in stream order');
    $firstSent = json_decode($transport->sent[0]['body'], true, 32, JSON_THROW_ON_ERROR);
    check(
        $firstSent['occurredAt'] === '2026-11-15T09:00:05.000Z' && (new SignedEventVerifier($secret))->verify($transport->sent[0]['body'], $nowSec)['occurredAt'] === $firstSent['occurredAt'],
        'roundtrip: transport body occurredAt is the signed wire string',
    );
    check(array_reduce($store2->ordered(), static fn (bool $ok, array $r): bool => $ok && $r['status'] === 'published', true), 'delivery: every row marked published');

    // A failed head blocks its tenant's later events; the other tenant is not held back.
    $store3 = new InMemoryBookingOutbox();
    $clock3 = new BookingOutboxTestClock($t0);
    $pub3 = new BookingEventPublisher($store3, $clock3->closure());
    $pub3->created($booking, 1);
    $pub3->rescheduled(['startAt' => '2026-11-21T10:00:00+11:00', 'endAt' => '2026-11-21T12:00:00+11:00'] + $booking, 2);
    $pub3->created(['bookingId' => 'bk9', 'companyId' => $companyB] + $booking, 1);
    $failures = 0;
    $transport3 = new RecordingBookingTransport(static function (string $type, string $org) use (&$failures, $companyA): bool {
        if ($type === 'booking_created' && $org === $companyA && $failures === 0) {
            $failures++;

            return true;
        }

        return false;
    });
    $dispatcher3 = new BookingEventDispatcher($store3, $transport3, $envelope, $clock3->closure());
    check($dispatcher3->drain(10) === 1, 'retry: only the other tenant publishes while the head is failing');
    $headRow = $store3->ordered()[0];
    check($headRow['status'] === 'pending' && $headRow['attempts'] === 1, 'retry: failed head stays pending with one attempt');
    check($headRow['last_error'] === 'transport_failed' && strpos((string) $headRow['last_error'], 'consumer.invalid') === false, 'retry: stored error is a fixed code, no URL');
    check($dispatcher3->drain(10) === 0, 'retry: nothing is sent before the backoff passes');
    $clock3->now = $t0->modify('+31 seconds');
    check($dispatcher3->drain(10) === 2, 'retry: after backoff the head and its later event go out in order');
    $orderA = [];
    foreach ($transport3->sent as $sent) {
        $decoded = (new SignedEventVerifier($secret))->verify($sent['body'], $nowSec);
        if ($decoded['organisationId'] === $companyA) {
            $orderA[] = $decoded['eventType'];
        }
    }
    check($orderA === ['booking_created', 'booking_rescheduled'], 'retry: failed event is sent again before anything after it');

    // A dead letter does not block its tenant.
    $store4 = new InMemoryBookingOutbox();
    $clock4 = new BookingOutboxTestClock($t0);
    $pub4 = new BookingEventPublisher($store4, $clock4->closure());
    $pub4->created($booking, 1);
    $pub4->rescheduled(['startAt' => '2026-11-21T10:00:00+11:00', 'endAt' => '2026-11-21T12:00:00+11:00'] + $booking, 2);
    $transport4 = new RecordingBookingTransport(static fn (string $type): bool => $type === 'booking_created');
    $dispatcher4 = new BookingEventDispatcher($store4, $transport4, $envelope, $clock4->closure());
    for ($i = 0; $i < BookingEventDispatcher::MAX_ATTEMPTS; $i++) {
        $clock4->now = $clock4->now->modify('+7200 seconds');
        $dispatcher4->drain(5);
    }
    $rows4 = $store4->ordered();
    check($rows4[0]['status'] === 'dead' && $rows4[0]['attempts'] === BookingEventDispatcher::MAX_ATTEMPTS, 'dead: head is dead after the attempt limit');
    check($rows4[1]['status'] === 'published', 'dead: later event of the same tenant goes out after a dead letter');

    check($dispatcher->drain(0) === 0, 'delivery: a zero limit sends nothing');

    // --- Transport URL rules (no connection is opened) ------------------------
    check(reasonOf(fn () => new App\Booking\Events\CurlBookingEventTransport('http://consumer.example/events')) === 'config_invalid', 'transport: plain http refused');
    check(reasonOf(fn () => new App\Booking\Events\CurlBookingEventTransport('https://user:pw@consumer.example/events')) === 'config_invalid', 'transport: credentials in URL refused');
    check(reasonOf(fn () => new App\Booking\Events\CurlBookingEventTransport('https://consumer.example/events?x=1')) === 'config_invalid', 'transport: query string refused');
    check(reasonOf(fn () => new App\Booking\Events\CurlBookingEventTransport('http://localhost:8080/events')) === 'config_invalid', 'transport: localhost http refused unless opted in');
    check(reasonOf(fn () => new App\Booking\Events\CurlBookingEventTransport('https://consumer.example/events')) === null, 'transport: https URL accepted');
}

runBookingOutboxChecks();
