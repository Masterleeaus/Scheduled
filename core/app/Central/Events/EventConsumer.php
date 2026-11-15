<?php
declare(strict_types=1);

namespace App\Central\Events;

use App\Central\CentralException;

/**
 * Accepts one signed Central event. Order: verify the signature, check the organisation, find the handler for
 * the event version, claim the idempotency key, run the handler. A duplicate returns 'duplicate' and runs nothing. A failed handler
 * releases its claim and the failure propagates, so Central retries.
 */
final class EventConsumer
{
    /**
     * @param array<string, callable(array<string,mixed>):void> $handlers keyed "<eventType>.v<schemaVersion>"
     * @param ?string $organisationId The one organisation this consumer serves. When set, an event for any other
     *                                organisation is refused after its signature verifies and before it is claimed.
     */
    public function __construct(
        private readonly SignedEventVerifier $verifier,
        private readonly ProcessedEventStore $store,
        private readonly string $consumerName,
        private readonly array $handlers,
        private readonly ?string $organisationId = null,
    ) {
    }

    /** @return 'processed'|'duplicate' */
    public function handle(string $rawBody, int $nowSeconds): string
    {
        $envelope = $this->verifier->verify($rawBody, $nowSeconds);
        if ($this->organisationId !== null && strtolower($envelope['organisationId']) !== strtolower($this->organisationId)) {
            throw new CentralException('tenant_mismatch', 'Event belongs to another organisation.');
        }
        $name = $envelope['eventType'].'.v'.$envelope['schemaVersion'];
        if (!isset($this->handlers[$name])) {
            throw new CentralException('unsupported_event', 'No handler for this event version.');
        }
        $key = $envelope['idempotencyKey'];
        if (!$this->store->claim($this->consumerName, $key, $envelope['eventId'])) {
            return 'duplicate';
        }
        try {
            ($this->handlers[$name])($envelope);
        } catch (\Throwable $e) {
            $this->store->release($this->consumerName, $key);
            throw $e;
        }

        return 'processed';
    }
}
