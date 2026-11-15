<?php
declare(strict_types=1);

namespace App\Booking\Events;

use App\Central\CentralException;

/**
 * POSTs the signed body to one Central consumer URL. https only; http is allowed for localhost when the caller opts
 * in. Redirects are not followed, and any non-2xx answer is a failure. The answer body is discarded.
 */
final class CurlBookingEventTransport implements BookingEventTransport
{
    private readonly string $url;

    public function __construct(string $url, private readonly int $timeoutSeconds = 10, bool $allowInsecureLocalhost = false)
    {
        $parts = parse_url($url);
        $scheme = is_array($parts) ? strtolower((string) ($parts['scheme'] ?? '')) : '';
        $host = is_array($parts) ? strtolower((string) ($parts['host'] ?? '')) : '';
        $localOk = $scheme === 'http' && $host === 'localhost' && $allowInsecureLocalhost;
        if (!is_array($parts) || (!($scheme === 'https' || $localOk)) || $host === ''
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) {
            throw new CentralException('config_invalid', 'Booking event endpoint must be an https URL without credentials, query or fragment.');
        }
        if ($timeoutSeconds < 1 || $timeoutSeconds > 60) {
            throw new CentralException('config_invalid', 'Booking event timeout must be 1 to 60 seconds.');
        }
        $this->url = $url;
    }

    public function send(string $body, string $eventId): void
    {
        if (strlen($body) > BookingEventEnvelope::MAX_BODY_BYTES) {
            throw new CentralException('body_too_large', 'Booking event body exceeds the limit.');
        }
        $ch = curl_init($this->url);
        if ($ch === false) {
            throw new CentralException('transport_failed', 'Booking event transport could not start.');
        }
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        ]);
        $ok = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($ok === false || $status < 200 || $status > 299) {
            throw new CentralException('transport_failed', 'Booking event consumer did not accept the event.');
        }
    }
}
