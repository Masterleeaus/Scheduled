<?php
declare(strict_types=1);

namespace App\Booking\Events;

use App\Central\CentralException;
use App\Central\Config\CentralConfig;
use App\Central\Events\CanonicalJson;

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
            'occurredAt' => (string) $row['occurred_at'],
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
