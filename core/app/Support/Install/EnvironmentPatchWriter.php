<?php
namespace App\Support\Install;

/** Minimal, strict .env patcher for the one-time setup form. Never logs secrets. */
final class EnvironmentPatchWriter
{
    public static function apply(string $path, array $replacements): void
    {
        if (!is_file($path) || !is_writable($path)) {
            throw new \RuntimeException('Environment file must exist and be writable.');
        }
        $lock = @fopen($path . '.install.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX)) {
            throw new \RuntimeException('Could not lock installation environment.');
        }
        try {
            $original = @file_get_contents($path);
            if (!is_string($original) || $original === '') {
                throw new \RuntimeException('Missing installation environment.');
            }
            $values = [];
            foreach ($replacements as $key => $value) {
                if (!preg_match('/^[A-Z][A-Z0-9_]*$/D', (string) $key) || !is_string($value)
                    || preg_match('/[\x00\r\n]/', $value)) {
                    throw new \InvalidArgumentException('Invalid environment configuration value.');
                }
                $values[$key] = self::quote($value);
            }
            // Never permit a stale/new key to replace the key already in use by this request.
            if (isset($values['APP_KEY'])) {
                if (!preg_match('/^APP_KEY=(base64:[A-Za-z0-9+\/=]+)\s*$/m', $original, $keyMatch)
                    || $values['APP_KEY'] !== $keyMatch[1]) {
                    throw new \RuntimeException('Application key changed during installation.');
                }
            }
            $lines = preg_split('/\r\n|\n|\r/', $original);
            $seen = [];
            $result = [];
            foreach ($lines as $line) {
                if (preg_match('/^([A-Z][A-Z0-9_]*)=/', $line, $match)
                    && array_key_exists($match[1], $values)) {
                    $key = $match[1];
                    if (isset($seen[$key])) {
                        // Remove duplicate definitions that would otherwise override setup.
                        continue;
                    }
                    $seen[$key] = true;
                    $result[] = $key . '=' . $values[$key];
                } else {
                    $result[] = $line;
                }
            }
            foreach ($values as $key => $value) {
                if (!isset($seen[$key])) {
                    $result[] = $key . '=' . $value;
                }
            }
            $serialized = rtrim(implode("\n", $result), "\n") . "\n";
            $temporary = $path . '.install-' . bin2hex(random_bytes(8));
            $bytes = @file_put_contents($temporary, $serialized, LOCK_EX);
            if ($bytes === false || $bytes !== strlen($serialized)) {
                @unlink($temporary);
                throw new \RuntimeException('Cannot write temporary environment file.');
            }
            @chmod($temporary, 0600);
            if (!@rename($temporary, $path)) {
                @unlink($temporary);
                throw new \RuntimeException('Cannot commit environment changes.');
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private static function quote(string $value): string
    {
        if ($value === '') {
            return '""';
        }
        // Escaping for vlucas/phpdotenv double-quoted values.
        if (preg_match('/^[A-Za-z0-9_.:\/\-@+%=,]+$/D', $value)) {
            return $value;
        }
        return '"' . str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value) . '"';
    }
}
