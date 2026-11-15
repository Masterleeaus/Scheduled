<?php

use App\Central\Http\CentralController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Titan Central routes
|--------------------------------------------------------------------------
|
| Loaded by App\Central\Providers\CentralServiceProvider only when
| CENTRAL_INTEGRATION_ENABLED is true. With the flag off, these paths do not exist.
|
| POST /api/central/events is not defined here. Inbound events are handled by the route in
| routes/api.php (CENTRAL_EVENTS_ENABLED), which is the single owner of that path.
*/

Route::middleware('api')
    ->prefix('api/central')
    ->name('central.')
    ->group(function () {
        Route::get('status', [CentralController::class, 'status'])->name('status');
    });
