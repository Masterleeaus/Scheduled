<?php
declare(strict_types=1);

namespace App\Central\Events;

/**
 * Records which events a consumer has handled. claimAndRun() claims the key and runs the effect as one unit:
 * it returns false without running the effect for a duplicate, and when the effect throws, the claim is undone
 * before the exception propagates. A failed effect therefore never leaves a claim that reads as success, so a
 * retry is processed rather than reported as a duplicate. claim() and release() remain the primitives.
 */
interface ProcessedEventStore
{
    /** @param callable():void $effect */
    public function claimAndRun(string $consumer, string $idempotencyKey, string $eventId, callable $effect): bool;

    public function claim(string $consumer, string $idempotencyKey, string $eventId): bool;

    public function release(string $consumer, string $idempotencyKey): void;
}
