<?php
namespace App\Support\Install;

/** Centralised MariaDB endpoint validation for a first-time installation. */
final class DatabaseEndpoint
{
    public static function port($value): int
    {
        // No silent fallback for malformed values: connecting to the wrong server
        // can result in non-transactional schema imports on an unintended database.
        if (!is_string($value) && !is_int($value)) {
            throw new \InvalidArgumentException('Database port must be an integer between 1 and 65535.');
        }
        $text = (string) $value;
        if (!preg_match('/^[1-9][0-9]{0,4}$/D', $text)) {
            throw new \InvalidArgumentException('Database port must be an integer between 1 and 65535.');
        }
        $port = (int) $text;
        if ($port < 1 || $port > 65535) {
            throw new \InvalidArgumentException('Database port must be an integer between 1 and 65535.');
        }
        return $port;
    }
}
