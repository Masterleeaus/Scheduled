<?php
declare(strict_types=1);

namespace App\Central\Http;

use App\Central\CentralException;

/** URL rules shared by discovery, OIDC, service calls and configuration. */
final class Origin
{
    /** Returns scheme://host[:port] for an absolute URL, or fails closed. */
    public static function of(string $url, bool $allowInsecureLocalhost): string
    {
        $parts = parse_url($url);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            throw new CentralException('invalid_url', 'A Central URL must be absolute.');
        }
        if (isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment'])) {
            throw new CentralException('invalid_url', 'A Central URL must not contain credentials or a fragment.');
        }
        $scheme = strtolower($parts['scheme']);
        $host = strtolower($parts['host']);
        $local = in_array($host, ['localhost', '127.0.0.1'], true);
        if (!($scheme === 'https' || ($scheme === 'http' && $local && $allowInsecureLocalhost))) {
            throw new CentralException('insecure_url', 'A Central URL must use https (http is allowed only for localhost in development).');
        }
        return $scheme.'://'.$host.(isset($parts['port']) ? ':'.$parts['port'] : '');
    }

    public static function assertSame(string $url, string $origin, bool $allowInsecureLocalhost): void
    {
        if (self::of($url, $allowInsecureLocalhost) !== $origin) {
            throw new CentralException('origin_mismatch', 'A Central endpoint is on a different origin than CENTRAL_ORIGIN.');
        }
    }
}
