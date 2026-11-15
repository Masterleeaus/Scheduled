<?php
/**
 * Installer boot key. Runs before Laravel loads environment/session configuration.
 * No rotation of an existing key and never repairs an installed site's missing key.
 */
final class InstallerBootKey
{
    public static function ensure(string $corePath): void
    {
        $corePath = rtrim($corePath, '/\\');
        $target = $corePath . '/.env';
        $installed = $corePath . '/storage/installed';
        if (is_file($installed)) {
            // Installed sites should be managed explicitly, never silently changed by bootstrap.
            return;
        }
        $handle = @fopen($target, 'c+');
        if (!$handle) {
            throw new RuntimeException('Installer requires a writable core/.env file.');
        }
        try {
            if (!flock($handle, LOCK_EX)) {
                throw new RuntimeException('Unable to lock installation environment.');
            }
            rewind($handle);
            $contents = stream_get_contents($handle);
            if ($contents === false) {
                throw new RuntimeException('Unable to read installation environment.');
            }
            if (trim($contents) === '') {
                $example = @file_get_contents($corePath . '/.env.example');
                if (!is_string($example) || $example === '') {
                    throw new RuntimeException('Missing core/.env.example for first installation.');
                }
                $contents = $example;
            }
            $found = preg_match('/^APP_KEY=([^\r\n]*)/m', $contents, $match);
            $previous = $found ? trim($match[1], " \t\"'") : '';
            if ($previous !== '') {
                // Never rotate a configured secret, even if it has a different encoding.
                return;
            }
            $key = 'base64:' . base64_encode(random_bytes(32));
            $contents = $found
                ? preg_replace('/^APP_KEY=[^\r\n]*/m', 'APP_KEY=' . $key, $contents, 1)
                : rtrim($contents) . "\nAPP_KEY=" . $key . "\n";
            // Fresh install must not inherit development debug settings.
            $contents = preg_replace('/^APP_DEBUG=[^\r\n]*/m', 'APP_DEBUG=false', $contents);
            $contents = preg_replace('/^APP_ENV=[^\r\n]*/m', 'APP_ENV=production', $contents);
            rewind($handle);
            if (!ftruncate($handle, 0)) {
                throw new RuntimeException('Unable to truncate installation environment.');
            }
            $written = fwrite($handle, $contents);
            if ($written === false || $written !== strlen($contents) || !fflush($handle)) {
                throw new RuntimeException('Unable to persist installation application key.');
            }
            @chmod($target, 0600);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
