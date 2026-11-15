<?php
declare(strict_types=1);

namespace App\Central\Events;

use App\Central\CentralException;

/**
 * Turns one raw Central delivery into an HTTP status and a JSON body. Nothing is read from the request except the
 * raw body, so the signature is checked over the exact bytes Central sent. The response never echoes the body,
 * the signature or the secret.
 */
final class EventIngress
{
    /** Stable reason codes mapped to HTTP status. Anything not listed is a server fault and returns 500 so Central retries. */
    private const STATUS = [
        'body_too_large' => 413,
        'payload_too_large' => 413,
        'unsigned' => 401,
        'bad_signature' => 401,
        'invalid_json' => 400,
        'invalid_envelope' => 400,
        'stale' => 400,
        'from_future' => 400,
        'tenant_mismatch' => 403,
        'unsupported_event' => 422,
        'config_missing' => 503,
        'config_invalid' => 503,
    ];

    public function __construct(
        private readonly EventConsumer $consumer,
        private readonly bool $enabled,
    ) {
    }

    /** @return array{0:int,1:array<string,string>} [status, body] */
    public function handle(string $rawBody, int $nowSeconds): array
    {
        if (!$this->enabled) {
            return [404, ['error' => 'not_found']];
        }
        try {
            $result = $this->consumer->handle($rawBody, $nowSeconds);

            return [200, ['status' => $result]];
        } catch (CentralException $e) {
            return [self::STATUS[$e->reason] ?? 500, ['error' => $e->reason]];
        } catch (\Throwable) {
            return [500, ['error' => 'handler_failed']];
        }
    }
}
