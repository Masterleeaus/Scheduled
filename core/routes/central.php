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
| SINGLE-INSTANCE ONLY: the event store behind /events is process-local (see config/central.php).
*/

Route::middleware('api')
    ->prefix('api/central')
    ->name('central.')
    ->group(function () {
        Route::get('status', [CentralController::class, 'status'])->name('status');
        Route::post('events', [CentralController::class, 'events'])->name('events');
    });
