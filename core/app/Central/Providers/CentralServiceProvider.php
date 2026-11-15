<?php
declare(strict_types=1);

namespace App\Central\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Titan Central wiring. config/app.php registers this provider only when CENTRAL_INTEGRATION_ENABLED is true.
 * The checks below repeat that gate so the provider does nothing if it is ever listed without the flag.
 * It loads the status routes only. The inbound event store is not bound here.
 */
final class CentralServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Nothing to bind. The status routes need no container services.
    }

    public function boot(): void
    {
        if (!$this->enabled()) {
            return;
        }

        $this->loadRoutesFrom(base_path('routes/central.php'));
    }

    private function enabled(): bool
    {
        return config('central.integration_enabled', false) === true;
    }
}
