<?php
declare(strict_types=1);

namespace App\Central\Events;

use App\Central\CentralException;
use App\Central\Config\CentralConfig;

/**
 * Verifies one Central outbox envelope. The signature is HMAC-SHA256 (hex) over the canonical JSON of every
 * field except `signature`, using the same canonical form as Central: object keys sorted at every depth,
 * no whitespace. Signature is checked first. Only then are the envelope fields and freshness checked.
 */
final class SignedEventVerifier
{
    public const MAX_BODY_BYTES = 16384;
    public const MAX_PAYLOAD_BYTES = 4096;

    public function __construct(
        private readonly string $secret,
        private readonly int $maxAgeSeconds = 86400,
        private readonly int $maxFutureSkewSeconds = 300,
    ) {
        if (strlen($secret) < CentralConfig::MIN_SECRET_LENGTH) {
            throw new CentralException('config_invalid', 'Event secret is shorter than the minimum length.');
        }
    }

    /** @return array<string,mixed> the verified envelope, as an array */
    public function verify(string $rawBody, int $nowSeconds): array
    {
        if (strlen($rawBody) > self::MAX_BODY_BYTES) {
            throw new CentralException('body_too_large', 'Event body is larger than allowed.');
        }
        try {
            $object = json_decode($rawBody, false, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new CentralException('invalid_json', 'Event body is not valid JSON.');
        }
        if (!$object instanceof \stdClass) {
            throw new CentralException('invalid_envelope', 'Event body is not a JSON object.');
        }
        $signature = $object->signature ?? null;
        if (!is_string($signature) || !preg_match('/\A[0-9a-f]{64}\z/', $signature)) {
            throw new CentralException('unsigned', 'Event has no valid signature.');
        }
        unset($object->signature);
        $expected = hash_hmac('sha256', self::canonicalJson($object), $this->secret);
        if (!hash_equals($expected, $signature)) {
            throw new CentralException('bad_signature', 'Event signature does not verify.');
        }

        $envelope = json_decode($rawBody, true, 32, JSON_THROW_ON_ERROR);
        unset($envelope['signature']);
        $this->assertEnvelope($envelope);

        $occurred = (new \DateTimeImmutable((string) $envelope['occurredAt']))->getTimestamp();
        if ($nowSeconds - $occurred > $this->maxAgeSeconds) {
            throw new CentralException('stale', 'Event is older than the accepted age.');
        }
        if ($occurred - $nowSeconds > $this->maxFutureSkewSeconds) {
            throw new CentralException('from_future', 'Event time is ahead of this clock.');
        }

        return $envelope;
    }

    /** Canonical JSON as Central computes it. Accepts stdClass objects and lists from json_decode. */
    public static function canonicalJson(mixed $value): string
    {
        return json_encode(self::sortKeys($value), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    private static function sortKeys(mixed $value): mixed
    {
        if ($value instanceof \stdClass) {
            $fields = get_object_vars($value);
            ksort($fields, SORT_STRING);
            $out = new \stdClass();
            foreach ($fields as $name => $item) {
                $out->{(string) $name} = self::sortKeys($item);
            }

            return $out;
        }
        if (is_array($value)) {
            return array_map([self::class, 'sortKeys'], $value);
        }

        return $value;
    }

    /** @param array<string,mixed> $envelope */
    private function assertEnvelope(array $envelope): void
    {
        $uuid = '/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/i';
        $slug = '/\A[a-z][a-z0-9_-]{0,63}\z/';
        $ok = is_string($envelope['eventId'] ?? null) && preg_match($uuid, $envelope['eventId'])
            && is_string($envelope['organisationId'] ?? null) && preg_match($uuid, $envelope['organisationId'])
            && is_string($envelope['sourceProduct'] ?? null) && preg_match($slug, $envelope['sourceProduct'])
            && is_string($envelope['eventType'] ?? null) && preg_match('/\A[a-z][a-z0-9_]{0,63}\z/', $envelope['eventType'])
            && is_int($envelope['schemaVersion'] ?? null) && $envelope['schemaVersion'] >= 1
            && is_string($envelope['idempotencyKey'] ?? null) && strlen($envelope['idempotencyKey']) >= 1
                && strlen($envelope['idempotencyKey']) <= 200
            && is_string($envelope['occurredAt'] ?? null)
            && is_array($envelope['payload'] ?? null);
        if (!$ok) {
            throw new CentralException('invalid_envelope', 'Event envelope is missing or has an invalid field.');
        }
        if (strlen(self::canonicalJson($envelope['payload'])) > self::MAX_PAYLOAD_BYTES) {
            throw new CentralException('payload_too_large', 'Event payload is larger than allowed.');
        }
    }
}
