<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\BasicSetting as BS;
use App\BasicExtended as BE;
use App\Social;
use App\Ulink;
use App\Page;
use App\Scategory;
use App\Language;
use App\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // The customer cannot register or configure this executor. Both the global
        // gate and the private per-tenant release-evidence checks must pass.
        if (config('booking_internal.public_commit_enabled', false) !== true) {
            return;
        }
        $profiles = config('booking_internal.public_commit_tenants', []);
        if (!is_array($profiles) || !$profiles) {
            return;
        }
        $this->app->singleton(\App\Booking\Public\Checkout\CheckoutSubmissionExecutor::class,
            static function () use ($profiles) {
                return new \App\Booking\Public\Checkout\NativeCheckoutSubmissionExecutor(
                    $profiles, dirname(base_path()) . '/private/booking-sidecar'
                );
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Installation routing belongs in HTTP middleware. Terminating here also
        // aborts Artisan commands, including fresh-install feature migrations.
    }
}
