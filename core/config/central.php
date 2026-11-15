<?php

/*
|--------------------------------------------------------------------------
| Titan Central integration
|--------------------------------------------------------------------------
|
| Master switch for the Central wiring. It defaults to OFF, so production
| behaviour is unchanged until an operator sets CENTRAL_INTEGRATION_ENABLED=true.
|
| When OFF:
|   - App\Central\Providers\CentralServiceProvider is not registered
|     (see the conditional entry in config/app.php);
|   - routes/central.php is not loaded, so no /api/central/* route exists.
|
| When ON:
|   - the provider loads routes/central.php;
|   - GET  /api/central/status   reports that the integration is enabled.
|
| Inbound events (POST /api/central/events) are not part of this flag. They are
| served by routes/api.php and gated by CENTRAL_EVENTS_ENABLED.
|
| CONFIG NOTE: SINGLE-INSTANCE ONLY. The link nonce store (scheduled/src/nonce-store.js)
| is process-local. Do not enable this flag on more than one app instance until a
| shared, durable store with the same semantics is in place.
|
| The flag is read from the environment only. Central's remote flag read
| endpoint is not live yet, so it is not consulted here.
|
*/

return [

    'integration_enabled' => filter_var(env('CENTRAL_INTEGRATION_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

];
