<?php
declare(strict_types=1);

namespace App\Central\Flags;

use App\Central\CentralException;

/**
 * Reads Central-governed feature flags. Every answer is a declared default unless a remote source returns a
 * valid, non-fallback value. A remote error, a timeout, a fallback answer, or a value of the wrong type all
 * return the default and never fail the request.
 *
 * Central's flag read endpoint is a contract, not yet live. Until it is, construct this reader with no remote
 * source, and the defaults are the only values.
 */
final class FlagReader
{
    /**
     * @param array<string,array{type:string, default:bool|string|int, allowed?:list<string>, min?:int, max?:int}> $declarations
     * @param null|\Closure(string):?array{value?:mixed, source?:string, revision?:?int} $remote
     */
    public function __construct(
        private readonly array $declarations,
        private readonly ?\Closure $remote = null,
    ) {
        foreach ($declarations as $key => $decl) {
            if (!self::valid($decl, $decl['default'])) {
                throw new CentralException('flag_declaration_invalid', "Declared default for flag {$key} does not match its type.");
            }
        }
    }

    public function read(string $key): bool|string|int
    {
        if (!array_key_exists($key, $this->declarations)) {
            throw new CentralException('undeclared_flag', 'Flag is not declared in this product.');
        }
        $decl = $this->declarations[$key];
        if ($this->remote === null) {
            return $decl['default'];
        }
        try {
            $answer = ($this->remote)($key);
        } catch (\Throwable) {
            return $decl['default'];
        }
        if (!is_array($answer) || ($answer['source'] ?? 'fallback') === 'fallback' || !array_key_exists('value', $answer)) {
            return $decl['default'];
        }

        return self::valid($decl, $answer['value']) ? $answer['value'] : $decl['default'];
    }

    /** @param array{type:string, allowed?:list<string>, min?:int, max?:int} $decl */
    public static function valid(array $decl, mixed $value): bool
    {
        return match ($decl['type']) {
            'boolean' => is_bool($value),
            'enum' => is_string($value) && in_array($value, $decl['allowed'] ?? [], true),
            'integer' => is_int($value)
                && $value >= ($decl['min'] ?? PHP_INT_MIN)
                && $value <= ($decl['max'] ?? PHP_INT_MAX),
            default => false,
        };
    }
}
