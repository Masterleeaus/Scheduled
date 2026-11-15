<?php
declare(strict_types=1);

namespace App\Booking\Events;

use App\Central\CentralException;
use App\Central\Config\CentralConfig;
use App\Central\Events\CanonicalJson;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Builds the signed envelope for one outbox row, as EVENTS.md describes. The signature is hex HMAC-SHA256 over the
 * canonical JSON of every other field, keyed by the route secret. The body is the envelope as canonical JSON.
 */
final class BookingEventEnvelope
{
    public const MAX_BODY_BYTES = 16384;

    public function __construct(private readonly string $secret)
    {
        if (strlen($secret) < CentralConfig::MIN_SECRET_LENGTH) {
            throw new CentralException('config_invalid', 'Booking event signing secret is too short.');
        }
    }

    /**
     * The one wire form for occurredAt: UTC, millisecond ISO-8601 with a Z suffix. Accepts the stored DATETIME(3)
     * text (`Y-m-d H:i:s.v`) and the ISO form, so the signed value and the sent value are the same string.
     */
    public static function wireTime(string $stored): string
    {
        if (preg_match('/^(\d{4}-\d{2}-\d{2})[ T](\d{2}:\d{2}:\d{2}\.\d{3})Z?$/', $stored, $m) !== 1) {
            throw new CentralException('invalid_payload', 'Outbox row occurred_at is not in a known form.');
        }
        $at = new DateTimeImmutable($m[1].'T'.$m[2], new DateTimeZone('UTC'));

        return $at->format('Y-m-d\TH:i:s.v\Z');
    }

    /** @param array<string,mixed> $row an outbox row as the store returns it */
    public function body(array $row): string
    {
        $payloadJson = (string) ($row['payload_json'] ?? '');
        if (!hash_equals((string) ($row['payload_digest'] ?? ''), hash('sha256', $payloadJson))) {
            throw new CentralException('invalid_payload', 'Outbox row payload does not match its digest.');
        }
        $payload = json_decode($payloadJson, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($payload)) {
            throw new CentralException('invalid_payload', 'Outbox row payload is not an object.');
        }

        $unsigned = [
            'eventId' => (string) $row['event_id'],
            'organisationId' => (string) $row['organisation_id'],
            'sourceProduct' => BookingEventType::SOURCE_PRODUCT,
            'eventType' => (string) $row['event_type'],
            'schemaVersion' => (int) $row['schema_version'],
            'correlationId' => (string) $row['correlation_id'],
            'idempotencyKey' => (string) $row['idempotency_key'],
            'occurredAt' => self::wireTime((string) $row['occurred_at']),
            'payload' => $payload,
        ];
        $signature = hash_hmac('sha256', CanonicalJson::encode($unsigned), $this->secret);
        $body = CanonicalJson::encode($unsigned + ['signature' => $signature]);
        if (strlen($body) > self::MAX_BODY_BYTES) {
            throw new CentralException('body_too_large', 'Booking event envelope exceeds the body limit.');
        }

        return $body;
    }
}
