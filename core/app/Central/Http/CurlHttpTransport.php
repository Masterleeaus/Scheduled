<?php
declare(strict_types=1);

namespace App\Central\Http;

use App\Central\CentralException;

/** HTTPS transport with no redirects, bounded timeouts and bounded response size. Bodies and headers are never logged. */
final class CurlHttpTransport implements HttpTransport
{
    private const MAX_RESPONSE_BYTES = 65536;

    public function __construct(
        private readonly int $timeoutSeconds = 10,
        private readonly bool $allowInsecureLocalhost = false,
    ) {
    }

    public function postForm(string $url, array $fields, array $headers = []): array
    {
        return $this->send($url, 'POST', http_build_query($fields, '', '&', PHP_QUERY_RFC3986), array_merge(
            ['Content-Type: application/x-www-form-urlencoded'],
            $headers,
        ));
    }

    public function getJson(string $url, array $headers = []): array
    {
        return $this->send($url, 'GET', null, $headers);
    }

    /** @return array{status:int, json:?array<mixed>} */
    private function send(string $url, string $method, ?string $body, array $headers): array
    {
        Origin::of($url, $this->allowInsecureLocalhost);
        $ch = curl_init($url);
        if ($ch === false) {
            throw new CentralException('transport_unavailable', 'The HTTP client could not start.');
        }
        $protocols = $this->allowInsecureLocalhost ? CURLPROTO_HTTPS | CURLPROTO_HTTP : CURLPROTO_HTTPS;
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_PROTOCOLS => $protocols,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $errno = curl_errno($ch);
        if ($raw === false || $errno !== 0) {
            throw new CentralException('transport_failed', 'A request to Central failed.');
        }
        $raw = (string) $raw;
        if (strlen($raw) > self::MAX_RESPONSE_BYTES) {
            throw new CentralException('response_too_large', 'Central answered with more data than allowed.');
        }
        $json = json_decode($raw, true);

        return ['status' => $status, 'json' => is_array($json) ? $json : null];
    }
}
