<?php

use App\Central\Config\CentralConfig;
use App\Http\Controllers\Central\CentralEventController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Central outbox delivery. Off unless CENTRAL_EVENTS_ENABLED is 1 or true, so the route is not registered by default.
if ((new CentralConfig())->eventsEnabled()) {
    Route::post('/central/events', [CentralEventController::class, 'receive'])
        ->name('central.events.receive');
}
