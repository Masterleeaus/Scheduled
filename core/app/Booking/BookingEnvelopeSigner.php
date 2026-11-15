<?php

namespace App\Booking;

use InvalidArgumentException;

/** HMAC contract for an internal-only sidecar; NOT public API-key authentication. */
final class BookingEnvelopeSigner
{
    public static function sign(string $secret, string $instanceKey, string $body, int $timestamp, string $nonce): string
    {
        if (strlen($secret) < 32 || $instanceKey === '' || !preg_match('/^[a-f0-9]{32}$/D', $nonce)) {
            throw new InvalidArgumentException('Invalid internal signing parameters.');
        }
        $canonical = implode("\n", [$instanceKey, (string) $timestamp, $nonce, hash('sha256', $body)]);
        return hash_hmac('sha256', $canonical, $secret);
    }

    public static function verify(string $secret, string $instanceKey, string $body, int $timestamp, string $nonce, string $signature, ?int $now = null): bool
    {
        $now = $now ?? time();
        if (abs($now - $timestamp) > 60 || !preg_match('/^[a-f0-9]{64}$/D', $signature)) {
            return false;
        }
        try {
            return hash_equals(self::sign($secret, $instanceKey, $body, $timestamp, $nonce), $signature);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }
}
