<?php
declare(strict_types=1);

namespace App\Central\Events;

use App\Central\CentralException;

/**
 * Canonical JSON as Central computes it for event signatures: object keys sorted at every depth, no whitespace,
 * slashes and non-ASCII characters left unescaped. Associative arrays are objects; lists keep their order.
 * Only null, bool, finite numbers, strings, lists and string-keyed arrays are accepted.
 */
final class CanonicalJson
{
    private const MAX_DEPTH = 32;

    /** @param array<array-key,mixed> $value */
    public static function encode(array $value): string
    {
        return json_encode(self::sort($value, 0), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    private static function sort(mixed $value, int $depth): mixed
    {
        if ($depth > self::MAX_DEPTH) {
            throw new CentralException('invalid_payload', 'Canonical JSON nesting is too deep.');
        }
        if ($value === null || is_string($value) || is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            if (!is_finite($value)) {
                throw new CentralException('invalid_payload', 'Canonical JSON numbers must be finite.');
            }

            return $value;
        }
        if (!is_array($value)) {
            throw new CentralException('invalid_payload', 'Canonical JSON values must be JSON types.');
        }
        if (array_is_list($value)) {
            return array_map(static fn (mixed $item): mixed => self::sort($item, $depth + 1), $value);
        }
        $keys = array_map('strval', array_keys($value));
        $combined = array_combine($keys, array_values($value));
        ksort($combined, SORT_STRING);
        $out = new \stdClass();
        foreach ($combined as $name => $item) {
            $out->{$name} = self::sort($item, $depth + 1);
        }

        return $out;
    }
}
