<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Stripe signed payload verifier. Never parse/re-encode payload before verifying. */
final class StripeTestSignature {
    public static function verify(string $payload, string $header, string $secret, int $now, int $tolerance = 300): void {
        if ($secret === '' || !str_starts_with($secret, 'whsec_') || strlen($secret) < 16 || strlen($payload) === 0 || strlen($payload) > 1048576 || strlen($header) > 2048 || $tolerance < 1 || $tolerance > 300) {
            throw new \InvalidArgumentException('Invalid payment webhook');
        }
        $parts = explode(',', $header); $timestamp = null; $signatures = [];
        foreach ($parts as $part) {
            $pair = explode('=', trim($part), 2);
            if (count($pair) !== 2) continue;
            if ($pair[0] === 't') {
                if ($timestamp !== null || !preg_match('/^[1-9][0-9]{0,15}$/D', $pair[1])) throw new \InvalidArgumentException('Invalid signature time');
                $timestamp = (int)$pair[1];
            } elseif ($pair[0] === 'v1' && preg_match('/^[a-f0-9]{64}$/D', $pair[1])) {
                $signatures[] = $pair[1];
            }
        }
        if ($timestamp === null || abs($now - $timestamp) > $tolerance || !$signatures) throw new \DomainException('Untrusted payment webhook');
        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        $valid = false;
        foreach ($signatures as $signature) $valid = hash_equals($expected, $signature) || $valid;
        if (!$valid) throw new \DomainException('Untrusted payment webhook');
    }
}
