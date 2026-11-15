<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

/**
 * Safe finalisation: first boot generates the key and the bundled schema importer
 * handles database setup. Never silently swallow errors or mutate either here.
 */
class FinalInstallManager
{
    public function runFinal(): string
    {
        // The legacy helper previously swallowed Artisan command exceptions.
        // Both commands are unsafe during finalisation: key rotation invalidates
        // the encrypted installer session and publish --all can overwrite files.
        if (config('installer.final.key') || config('installer.final.publish')) {
            throw new \RuntimeException('Unsafe installer finalisation commands are enabled.');
        }

        $key = (string) config('app.key');
        if (!\App\Support\Install\InstallationCompletionGuard::keyIsValid($key)) {
            throw new \RuntimeException('A valid application key is required before completion.');
        }

        return 'Installation prerequisites checked; no keys rotated or vendor files overwritten.';
    }
}
