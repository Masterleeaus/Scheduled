<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

/**
 * Create the one-time installation marker atomically.
 * The legacy helper appended to an existing marker, allowing two concurrent
 * completion requests to report success after both passed controller preflight.
 */
class InstalledFileManager
{
    public function create()
    {
        $path = storage_path('installed');
        // Mode x is O_CREAT|O_EXCL: exactly one competing installer can win.
        // Never append to a marker belonging to a previous successful install.
        $handle = @fopen($path, 'x');
        if ($handle === false) {
            throw new \RuntimeException('Installation is already finalised or the marker cannot be created.');
        }
        $message = trans('installer_messages.installed.success_log_message') . date('Y/m/d H:i:s') . "\n";
        try {
            if (!flock($handle, LOCK_EX)) {
                throw new \RuntimeException('Unable to lock the installation marker.');
            }
            $length = strlen($message);
            $written = 0;
            while ($written < $length) {
                $chunk = fwrite($handle, substr($message, $written));
                if ($chunk === false || $chunk === 0) {
                    throw new \RuntimeException('Unable to write the installation marker.');
                }
                $written += $chunk;
            }
            if (!fflush($handle)) {
                throw new \RuntimeException('Unable to flush the installation marker.');
            }
            @chmod($path, 0600);
            return $message;
        } catch (\Throwable $e) {
            // The exclusive handle belongs to this request: remove an incomplete
            // marker so a corrected installation may safely be retried.
            @unlink($path);
            throw $e;
        } finally {
            fclose($handle);
        }
    }

    public function update()
    {
        // Kept for compatibility with the existing FinalController call site.
        return $this->create();
    }
}
