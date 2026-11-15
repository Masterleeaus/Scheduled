<?php
namespace Titan\BookingSidecar;
use InvalidArgumentException;
final class Envelope {
    public static function verify(string $secret, string $key, string $body, array $headers, int $now): string {
        $ts = $headers['x-booking-time'] ?? '';
        $nonce = $headers['x-booking-nonce'] ?? '';
        $signature = $headers['x-booking-signature'] ?? '';
        if (strlen($secret) < 32 || !ctype_digit((string)$ts) || !preg_match('/^[a-f0-9]{32}$/D', $nonce)
            || !preg_match('/^[a-f0-9]{64}$/D', $signature) || abs($now - (int)$ts) > 60) {
            throw new InvalidArgumentException('Invalid internal request envelope.');
        }
        $signed = implode("\n", [$key, (string)$ts, $nonce, hash('sha256', $body)]);
        if (!hash_equals(hash_hmac('sha256', $signed, $secret), $signature)) {
            throw new InvalidArgumentException('Invalid internal signature.');
        }
        return $nonce;
    }
}
