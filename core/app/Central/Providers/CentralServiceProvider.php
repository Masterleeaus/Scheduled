<?php
declare(strict_types=1);

namespace App\Central\Providers;

use App\Central\Events\InMemoryProcessedEventStore;
use App\Central\Events\ProcessedEventStore;
use Illuminate\Support\ServiceProvider;

/**
 * Titan Central wiring. config/app.php registers this provider only when CENTRAL_INTEGRATION_ENABLED is true.
 * The checks below repeat that gate so the provider does nothing if it is ever listed without the flag.
 *
 * SINGLE-INSTANCE ONLY: the event store bound here is InMemoryProcessedEventStore. It is process-local and
 * not durable, so it gives no idempotency across requests or instances. Replace it with a database-backed
 * store, where claim and the effect commit in one transaction, before running more than one instance.
 */
final class CentralServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (!$this->enabled()) {
            return;
        }

        // SINGLE-INSTANCE ONLY. See the class comment and config/central.php.
        $this->app->singleton(ProcessedEventStore::class, InMemoryProcessedEventStore::class);
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
